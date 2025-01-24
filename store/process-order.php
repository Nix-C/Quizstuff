<?php
  require_once './config/database.php';
  require_once 'generate-invoice.php';
  require_once 'send-order-invoice.php';
  require_once 'send-user-invoice.php';
  $OK = true; // Make false if something is wrong

  // Get orderData object from the request body (which is a string in JSON format)
  $orderData = json_decode(file_get_contents('php://input')); 

  $shippingInfo = $orderData->shippingInfo;
  $lineItems = $orderData->lineItems;

  // Step 1 - Get item data

  // Get unique product ids
  $productIds = array_map(function($lineItem) {
    return $lineItem->productId;
  } , $lineItems);
  $productIds = array_unique($productIds);
  $productIds_string = implode(",", $productIds);

  // Get unique variant ids
  $variantIds = array_map(function ($lineItem) {
    return $lineItem->variantId;
  }, $lineItems);
  $variantIds = array_unique($variantIds);
  $variantIds = array_filter($variantIds, fn($id) => !is_null($id));
  $variantIds_string = implode(",", $variantIds);
  // Get unique option ids
  $optionIds = array_map(function($lineItem) {
    return $lineItem->optionId;
  }, $lineItems);
  $optionIds = array_unique($optionIds);
  $optionIds = array_filter($optionIds, fn($id) => !is_null($id));
  $optionIds_string = implode(',', $optionIds);
  
  $sql_get_data = "SELECT id, name, price, weight FROM products WHERE id IN (${productIds_string});";

  if($variantIds_string){
    $sql_get_data .= "SELECT id, name, price, weight FROM product_variants WHERE id IN (${variantIds_string});";
  }
  if($optionIds_string){
    $sql_get_data .= "SELECT id, name, price FROM product_options WHERE id IN (${optionIds_string});";
  }

  // Send query to DB
  $result = mysqli_multi_query($conn, $sql_get_data);

  // Declare product, variant, option data
  $productData = null;
  $variantData = null;
  $optionData = null;

  // Capture first result (always products)
  if($result = mysqli_store_result($conn)) {
    $productData = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $productData = json_decode(json_encode($productData));
    mysqli_free_result($result);
  }

  // Capture second (if exists), variants or options
  if(mysqli_next_result($conn) and $result = mysqli_store_result($conn)) {
    if($variantIds_string){
      $variantData = mysqli_fetch_all($result, MYSQLI_ASSOC);
      $variantData = json_decode(json_encode($variantData));
    } else if($optionIds_string) {
      $optionData = mysqli_fetch_all($result, MYSQLI_ASSOC);
      $optionData = json_decode(json_encode($optionData));
    }
    mysqli_free_result($result);
  }

  // Capture third (if exists), always options
  if(mysqli_next_result($conn) and $result = mysqli_store_result($conn)){
    $optionData = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $optionData = json_decode(json_encode($optionData));
    mysqli_free_result($result);
  }


  // Calculate total pre-tax price (and pre-ship)
  $total = 0.00;

  foreach($lineItems as $lineItem) {

    $target_product = array_filter($productData, function ($product) use ($lineItem) {
      return $product->id == $lineItem->productId;
    });

    if(isset($lineItem->optionId)){
      $target = array_filter($optionData, function ($option) use ($lineItem) {
        return $option->id == $lineItem->optionId;
      });
      
    } else if(isset($lineItem->variantId)){
      // Let variant target override product target
      $target = array_filter($variantData, function ($variant) use ($lineItem) {
        return $variant->id == $lineItem->variantId && $variant->price != null;
      });

    }
    $target = !empty($target) ? $target : $target_product;
    $target = array_values($target)[0];
    $target_product = array_values($target_product)[0];

    // Get item data
    $p = floatval($target->price);
    $q = floatval($lineItem->quantity);
    $total_p = $p * $q;

    // Assign prices
    $lineItem->price = $p;
    $lineItem->totalPrice = $total_p;
    $total += $total_p;

    $target = null;
  }


  // Step 2 - Push order to DB (return order ID)
  $sql = "INSERT INTO orders (name_first, name_last, address_1, address_2, city, state, zip, phone, email, total_price) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sssssssssd", 
      $shippingInfo->nameFirst, 
      $shippingInfo->nameLast, 
      $shippingInfo->address1, 
      $shippingInfo->address2,
      $shippingInfo->city, 
      $shippingInfo->state, 
      $shippingInfo->zip, 
      $shippingInfo->phone, 
      $shippingInfo->email, 
      $total
  );

  if($stmt->execute()) {
    echo "Order inserted successfully!";
  } else {
    echo "Error: " . mysqli_error($conn);
    $OK = false;
  };

  // Get the order ID
  $orderId = $conn->insert_id;

  // Step 3 - Push order items to DB (use order ID)
  if(isset($orderId)){
    $sql_insert_items = "INSERT INTO order_items (order_id, product_id, variant_id, option_id, quantity, price, total_price) VALUES ";
    $values = [];

    foreach($lineItems as $lineItem) {
      // Convert null values to 'NULL' (to prevent syntax errors in SQL)
      $variantId = isset($lineItem->variantId) ? $lineItem->variantId : 'NULL';
      $optionId = isset($lineItem->optionId) ? $lineItem->optionId : 'NULL';
      $productId = $lineItem->productId;
      $quantity = $lineItem->quantity;
      $price = $lineItem->price;
      $totalPrice = $lineItem->totalPrice;

      $values[] = "(${orderId}, ${productId}, ${variantId}, ${optionId}, ${quantity}, ${price}, ${totalPrice})";
      
    }

    // Build final SQL query string
    $sql_insert_items .= implode(", ", $values);

    // // Execute the query
    if (mysqli_query($conn, $sql_insert_items)) {
      echo "Order items inserted successfully!";
    } else {
      echo "Error: " . mysqli_error($conn);
      $OK = false;
    }    
  } else {
    echo "Error: Order ID not retrieved.";
    $OK = false;
  }

  // Step 4 - Call generate-invoice.php (pass $orderId)
  $invoice = generateInvoice($orderId);
  if(is_null($invoice)){
    echo "Error: Invoice could not be generated";
    $OK = false;
  }

  // Step 5 - Send mail with invoice body
  $qsEmail = "quizstuff@quizstuff.com";
  if(!sendOrderInvoice($invoice, $orderId, $qsEmail)){
    echo "Error: Invoice could not be sent to admin.";
    $OK = false;
  }

  $userEmail = $shippingInfo->email;
  if(!sendUserInvoice($invoice, $orderId, $userEmail)){
    echo "Error: Invoice could not be sent to customer.";
    $OK = false;
  }


  // Send a 200 OK HTTP status code
  if($OK) {
    http_response_code(200);
  } else {
    http_response_code(500);
  }
?>