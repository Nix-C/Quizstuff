<!-- // This is a test file for the order-form.php' js submission -->
<?php
  include 'config/database.php';

  $OK = true; // Make false if something is wrong

  // Get orderData object from the request body (which is a string in JSON format)
  $orderData = json_decode(file_get_contents('php://input')); 

  $shippingInfo = $orderData->shippingInfo;
  $lineItems = $orderData->lineItems;

  // Step 1 - Calculate pre-tax, pre-ship total

  // Get unique product ids
  $productIds = array_map(function($lineItem) {
    return $lineItem->productId;
  } , $lineItems);
  $productIds = array_unique($productIds);
  
  // Get unique variant ids
  $variantIds = array_map(function ($lineItem) {
    return $lineItem->variantId;
  }, $lineItems);
  $variantIds = array_unique($variantIds);
  $variantIds = array_filter($variantIds, fn($id) => !is_null($id));
  
  // Get unique option ids
  $optionIds = array_map(function($lineItem) {
    return $lineItem->optionId;
  }, $lineItems);
  $optionIds = array_unique($optionIds);
  $optionIds = array_filter($optionIds, fn($id) => !is_null($id));
  
  
  // 

  $total = 100.00;

  // TODO: Add address_2
  // Step 1 - Push order to DB (return order ID)
  $sql = "INSERT INTO orders (name_first, name_last, address_1, address_2, city, state, zip, phone, email, total_price) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ssssssssd", 
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
  // if($stmt->execute()) {
  //   echo "Order inserted successfully!";
  // } else {
  //   echo "Error: " . mysqli_error($conn);
  //   $OK = false;
  // };

  // Get the order ID
  //$orderId = $conn->insert_id;

  // TODO: Calculate and push price!
  // Step 2 - Push order items to DB (use order ID)
  if(isset($orderId)){
    $sql_insert_items = "INSERT INTO order_items (order_id, product_id, variant_id, option_id, quantity) VALUES ";
    $values = [];

    foreach($lineItems as $lineItem) {
      // Convert null values to 'NULL' (to prevent syntax errors in SQL)
      $variantId = isset($lineItem->variantId) ? $lineItem->variantId : 'NULL';
      $optionId = isset($lineItem->optionId) ? $lineItem->optionId : 'NULL';
      $productId = $lineItem->productId;
      $quantity = $lineItem->quantity;

      $values[] = "(${orderId}, ${productId}, ${variantId}, ${optionId}, ${quantity})";
      
    }

    // Build final SQL query string
    $sql_insert_items .= implode(", ", $values);

    // // Execute the query
    // if (mysqli_query($conn, $sql_insert_items)) {
    //   echo "Order items inserted successfully!";
    // } else {
    //   echo "Error: " . mysqli_error($conn);
    //   $OK = false;
    // }    
  } else {
    echo "Error: Order ID not retrieved.";
    $OK = false;
  }

  // Step n - Call generate-invoice.php (pass data above)
  // generateInvoice(a,b,c)
  // Return static html
  // sendMail()
  // Returns true if successful

  // Send a 200 OK HTTP status code
  if($OK) {
    http_response_code(200);
  } else {
    http_response_code(500);
  }
?>
<!-- Invoice Template -->
<!-- <pre><?= var_dump($shippingInfo) ?></pre>
<pre><?= var_dump($lineItems) ?></pre> -->
<pre><?= var_dump($productIds) ?></pre>
<pre><?= var_dump($variantIds) ?></pre>
<pre><?= boolval($optionIds) ?></pre>