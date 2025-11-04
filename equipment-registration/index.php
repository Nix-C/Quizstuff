<?php 
  include 'config/database.php';
  $page_title = "Equipment Registration";

  # Get Events
  $sql_events = "SELECT * FROM events";
  $result = mysqli_query($conn, $sql_events);
  $events = mysqli_fetch_all($result, MYSQLI_ASSOC);

  
?>

<!DOCTYPE html>
<html>
  <?php include '../head.php'; ?>
  <body>

    <?php include '../header.php'; ?>
    <?php include '../footer.php' ?>
  </body>
  
</html>