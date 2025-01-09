<?php
  // This is a test file for the order-form.php' js submission
  
  // Get orderData object from the request body (which is a string in JSON format)
  $orderData = json_decode(file_get_contents('php://input')); 


  // Do something here!
  $shippingInfo = $orderData->shippingInfo;
  $lineItems = $orderData->lineItems;

  // Send a 200 OK HTTP status code
  // http_response_code(200);
?>
<!-- Invoice Template -->
<h1>Invoice #00 </h1>
<ul>
  <li>First Name: <?= $shippingInfo-> nameFirst ?></li>
  <li>Last Name: <?= $shippingInfo-> nameLast ?></li>
</ul>