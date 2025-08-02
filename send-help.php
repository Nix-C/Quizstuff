<?php
  require_once 'script.php';
  $OK = true;
  $emailData = json_decode(file_get_contents('php://input')); 
  file_put_contents('debug.txt', file_get_contents('php://input'));

  if (!isset($emailData->name, $emailData->email, $emailData->message)) {
    http_response_code(400);
    exit('Missing required fields.');
  }

    $subject = "Quizstuff Help - ". $emailData->name;
    $res = sendMail($emailData->email, $subject, $emailData->message);
    
    if($res != "Success.") {
      $OK = false;
    }

  // Send a 200 OK HTTP status code
  if($OK) {
    http_response_code(200);
  } else {
    http_response_code(500);
  }
?>