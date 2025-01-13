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
      $sql_get_order = "SELECT * FROM orders WHERE id = ${orderId};"
      $result_order = mysqli_query($conn, $sql_get_items);
      $orderData = mysqli_fetch_all($result_order, MYSQLI_ASSOC);

      // Process $lineItems and $orderData to HTML table
      // 🔥

      return "An invoice";
    } else {
      echo "ERROR: No order_id provided.";
      return null;
    }
  }
  
?>