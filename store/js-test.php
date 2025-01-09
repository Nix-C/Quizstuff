<!-- // This is a test file for the order-form.php' js submission -->
<?php
  include 'config/database.php';

  // Get orderData object from the request body (which is a string in JSON format)
  $orderData = json_decode(file_get_contents('php://input')); 

  // Do something here!
  $shippingInfo = $orderData->shippingInfo;
  $lineItems = $orderData->lineItems;

  // Build SQL query

  // Set empty arrays
  $productIds = array();
  $variantIds = array();
  $optionIds = array();

  // Assemble product, variant, and option ids
  foreach ($lineItems as $lineItem) {
    // Push item id to the array
    array_push($productIds, $lineItem->id);

    // Push variant ids to the array
    if(isset($lineItem->variants)) {
      foreach ($lineItem->variants as $variant) {
        array_push($variantIds, $variant->id);
      }
    }

    // Push option ids to the array
    if(isset($lineItem->options)) {
      foreach ($lineItem->options as $option) {
        array_push($optionIds, $option->id);
      }
    }
  }
  $productIds_string = implode(", ", $productIds);
  $variantIds_string = implode(", ", $variantIds);
  $optionIds_string = implode(", ", $optionIds);


  $query = "SELECT 
      id, name, price
    FROM products 
    WHERE id IN ($productIds_string);";

  if($variantIds_string) {
    $query .= " SELECT 
      id, name, price
    FROM product_variants 
    WHERE id IN ($variantIds_string);";
  }

  if($optionIds_string) {
    $query .= " SELECT 
      id, name, price
    FROM product_options 
    WHERE id IN ($optionIds_string);";
  }

  $result = mysqli_multi_query($conn, $query);

  $products = null;
  $variants = null;
  $options = null;

  // Capture first result (always products)
  if($result = mysqli_store_result($conn)) {
    $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
  }
  
  // Capture second (if exists), variants or options
  if(mysqli_next_result($conn) and $result = mysqli_store_result($conn)) {
    if($variantIds_string){
      $variants = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else if($optionIds_string) {
      $options = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    mysqli_free_result($result);
  }

  // Capture third (if exists), always options
  if(mysqli_next_result($conn) and $result = mysqli_store_result($conn)){
    $options = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
  }

  // Init total variable
  $total = 0;

  // Send a 200 OK HTTP status code
  // http_response_code(200);
?>
<!-- Invoice Template -->
<!-- <pre><?= $query?></pre> -->
<!-- <pre><?= var_dump($orderData) ?></pre> -->
<!-- <h2>Products</h2>
<pre>
  <?= var_dump($products) ?>
</pre>
<h2>Variants</h2>
<pre>
  <?= var_dump($variants) ?>
</pre>
<h2>Options</h2>
<pre>
  <?= var_dump($options) ?>
</pre> -->

<!-- <pre><?= var_dump($lineItems) ?></pre> -->

<h1>Invoice #00 </h1>
<table>
  <tr>
    <th>Product Name</th>
    <th>Quantity</th>
    <th>Price</th>
  </tr>
  <?php foreach($lineItems as $lineItem) : ?>
    <?php // Get product data 
      $filtered_products = array_filter($products, function($value ) use ($lineItem) {
        return $value['id'] == $lineItem->id;
      });
      $product_data = reset($filtered_products);
      $product_data = json_decode(json_encode($product_data));

      // Set variables 
      $price = !is_null($product_data->price) ? $product_data->price : "";
      $qty = !is_null($lineItem->quantity) ? $lineItem->quantity : "";
    ?>
    <?php if(isset($lineItem->variants)) :?>
      <?php foreach($lineItem->variants as $variant) :?>
      <?php // Get $variant_data from $variants table
        $filtered_variants = array_filter($variants, function($value ) use ($variant) {
          return $value['id'] == $variant->id;
        });
        $variant_data = reset($filtered_variants);
        $variant_data = json_decode(json_encode($variant_data));

        // Override variables 
        $price = !is_null($variant_data->price) ? $variant_data->price : $price;
        $qty = !is_null($variant->quantity) ? $variant->quantity : "";
      ?>
      <tr>
        <td><?= $product_data->name?> - <?= $variant_data->name ?></td>
        <td><?= $qty ?></td>
        <td>$<?= $price ?></td>
      </tr>
      <?php // Add to total
        $total += ($price * $qty)
      ?>        
      <?php endforeach ?>
    <?php else : ?>
    <tr>
      <td><?= $product_data->name?></td>
      <td><?= $qty ?></td>
      <td>$<?= $price ?></td>
    </tr>
    <?php // Add to total
      $total += ($price * $qty)
    ?>
    <?php endif ?>

  <?php endforeach ?>
  <tr>
    <td 
      style="text-align: right"
      colspan="3"
    >Total: $<?= $total ?>
    </td>
  </tr>
</table>