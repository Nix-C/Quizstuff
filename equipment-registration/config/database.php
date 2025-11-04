<?php 
  define('DB_SERVER', 'localhost');
  define('DB_USERNAME', 'root');
  define('DB_PASSWORD', '');
  define('DB_NAME', 'equipment_manager');

    // Create connection 
  $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    // Check connection
  if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
  }
?>

