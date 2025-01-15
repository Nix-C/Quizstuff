<?php 
  function generateInvoice($orderId){
    include 'config/database.php';
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
      FROM quizstuff.order_items oi
      LEFT JOIN quizstuff.products p ON oi.product_id = p.id
      LEFT JOIN quizstuff.product_variants pv ON oi.variant_id = pv.id
      LEFT JOIN quizstuff.product_options po ON oi.option_id = po.id
      WHERE oi.order_id = ${orderId};
      ";

      $result_items = mysqli_query($conn, $sql_get_items);
      $lineItems = mysqli_fetch_all($result_items, MYSQLI_ASSOC);

      // Get $orderData
      $sql_get_order = "SELECT * FROM orders WHERE id = ${orderId};";
      $result_order = mysqli_query($conn, $sql_get_order);
      $orderData = mysqli_fetch_all($result_order, MYSQLI_ASSOC);

      // Process $lineItems and $orderData to HTML table
      // 🔥
      
      // Start building HTML invoice
      $invoice = '<h1>Invoice</h1>';
      $invoice .= '<table border="1" cellspacing="0" cellpadding="5">';
      $invoice .= '<tr><th>Item</th><th>Quantity</th><th>Price</th><th>Total</th></tr>';

      // Process $lineItems to build rows
      foreach ($lineItems as $item) {
          $name = htmlspecialchars($item['product_name'] ?: $item['variant_name'] ?: $item['option_name']);
          $quantity = intval($item['quantity']);
          $price = floatval($item['price']);
          $total = floatval($item['total_price']);

          $invoice .= "<tr><td>{$name}</td><td>{$quantity}</td><td>$" . number_format($price, 2) . "</td><td>$" . number_format($total, 2) . "</td></tr>";
      }

      // Calculate total price
      $total_price = array_sum(array_column($lineItems, 'total_price'));

      // Add total row
      $invoice .= '<tr><td colspan="3" align="right"><strong>Total:</strong></td><td><strong>$' . number_format($total_price, 2) . '</strong></td></tr>';
      $invoice .= '</table>';

      return $invoice;

      // For testing 🚧
      //echo print_r($lineItems) . "<br>" . "<br>";
      //echo print_r($orderData) . "<br>" . "<br>";

      //return "An invoice";
    } else {
      echo "ERROR: No order_id provided.";
      return null;
    }
    
  }

  // For testing 🚧
  echo print_r(generateInvoice(1));
?>