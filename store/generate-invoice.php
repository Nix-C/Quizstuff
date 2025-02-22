<?php 
  
  function generateInvoice($orderId){
    require './config/database.php';
    $DB_NAME = DB_NAME;
    if($orderId){

      // Get $lineItems 
      // 🤖
      $sql_get_items = "SELECT 
          p.name AS product_name,
          pv.name AS variant_name,
          po.name AS option_name,
          oi.quantity,
          oi.price,
          (oi.quantity * oi.price) AS total_price
      FROM ${DB_NAME}.order_items oi
      LEFT JOIN ${DB_NAME}.products p ON oi.product_id = p.id
      LEFT JOIN ${DB_NAME}.product_variants pv ON oi.variant_id = pv.id
      LEFT JOIN ${DB_NAME}.product_options po ON oi.option_id = po.id
      WHERE oi.order_id = ${orderId};
      ";

      $result_items = mysqli_query($conn, $sql_get_items);
      $lineItems = mysqli_fetch_all($result_items, MYSQLI_ASSOC);


      // Get $orderData
      $sql_get_order = "SELECT * FROM orders WHERE id = ${orderId};";
      $result_order = mysqli_query($conn, $sql_get_order);
      $orderData = mysqli_fetch_all($result_order, MYSQLI_ASSOC);
      // For some reason this is an array of arrays
      // ...They both are, but $lineItems doesn't seem to care.
      $orderData = $orderData[0]; 

      // Process $lineItems and $orderData to HTML table
      ob_start();
      ?>
      <div id="invoice">
        <table>
        <tr>
          <th>Product Name</th>
          <th>Quantity</th>
          <th>Price</th>
          <th>Total</th>
        </tr>
        <?php foreach($lineItems as $lineItem) : ?>
          <?php 
            if($lineItem["option_name"]){
              $name = $lineItem["option_name"];
            } else {
              $name = $lineItem["variant_name"] ?
              $lineItem["product_name"] . " - " . $lineItem["variant_name"] : 
              $lineItem["product_name"];
            }
          ?>
          <tr>
            <td><?= $name ?></td>
            <td><?= $lineItem["quantity"] ?></td>
            <td>$<?= $lineItem["price"] ?></td>
            <td>$<?= $lineItem["total_price"] ?></td>
          </tr>
        <?php endforeach ?>
        <tr>
          <td colspan="3" align="right">
            <strong>Total:</strong>
          </td>
          <td><strong>$<?= $orderData["total_price"] ?></strong></td>
        </tr>
        </table>
        <div>
          <label><strong>Name: </strong>
            <?= $orderData["name_first"] . " " . $orderData["name_last"] ?>
          </label><br>
          <label><strong>Address: </strong><br>
            <span>
              <?= $orderData["address_1"] ?>
              <?= $orderData["address_2"] ? $orderData["address_2"] : ""?> <br>
              <?= $orderData["city"] ?>, <?= $orderData["state"] ?> <?= $orderData["zip"] ?>
            </span>
          </label><br>
          <label><strong>Phone:</strong>
            <span> <?= $orderData["phone"] ?> </span>
          </label><br>
          <label><strong>Email:</strong>
            <?= $orderData["email"] ?>
          </label>
        </div>
      </div>
      <?php 
      $invoice = ob_get_clean();
      
      //return "An invoice";
      return $invoice;

    } else {
      echo "ERROR: No order_id provided.";
      return null;
    }

  }

  // For testing 🚧
  // echo print_r(generateInvoice(1));
?>