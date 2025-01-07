<?php
  // This is a test file for the order-form.php' js submission
  
  // Get orderData object from the request body
  $data = file_get_contents('php://input'); 

  // Do something here!
  echo "<h1>Data: <?= var_dump($data) ?> </h1>";

  // Send a 200 OK HTTP status code
  http_response_code(200);
?>