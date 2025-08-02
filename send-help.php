<?php
  use PHPMailer\PHPMailer\Exception;
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\SMTP;

  // Uncomment for local testing
  require_once 'PHPMailer/src/Exception.php';
  require_once 'PHPMailer/src/PHPMailer.php';
  require_once 'PHPMailer/src/SMTP.php';
  require_once 'config.php';
  
  $OK = true;
  $emailData = json_decode(file_get_contents('php://input')); 
  file_put_contents('debug.txt', file_get_contents('php://input'));
  $name = $emailData->name;

  if (!isset($emailData->name, $emailData->email, $emailData->message)) {
    http_response_code(400);
    exit('Missing required fields.');
  }


  $body = "
    <p>Hello, $name!</p>
    <p>Thank you for your message. We try to respond within 1–2 business days.</p>
    <p>Feel free to reply with further questions!</p>
    <p>Kind regards,<br>Quizstuff Team</p>
    <hr>
    <p><strong>Message:</strong></p>
    <p>" . nl2br(htmlspecialchars($emailData->message)) . "</p>
  ";

    $subject = "Quizstuff Help - ". $emailData->name;    
    $OK = sendMail($emailData->email, $subject, $body);



    function sendMail($to, $subject, $txt){

      // Create a new mail instance
      $mail = new \PHPMailer\PHPMailer\PHPMailer();

      // Send using SMTP Protocol
      $mail->IsSMTP();

      // Authenticate Gmail
      $mail->SMTPAuth = true;

      // Set Host from config file
      $mail->Host = MAILHOST;

      // Get Username
      $mail->Username = USERNAME;

      // Get Password
      $mail->Password = PASSWORD;

      // Use TTLS encryption
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

      // TCP port number
      $mail->Port = 587;

      // From
      $mail->SetFrom(SEND_FROM, SEND_FROM_NAME);

      // To
      $mail->AddAddress($to);
      $mail->AddAddress("quizstuff@quizstuff.com");

      // Reply info
      $mail->addReplyTo(REPLY_TO, REPLY_TO_NAME);

      // Support HTML in email
      $mail->IsHTML(true);

      // Subject
      $mail->Subject = $subject;

      // Body
      $mail->Body = $txt;

      // Body Non-HTML Alternate
      $mail->AltBody = $txt;

      //Debugging
      //$mail->SMTPDebug = 3;

      // Send the email
      if(!$mail->send()){
          return false;
      } else {
          return true;
      }

    }

      // Send a 200 OK HTTP status code
  if($OK) {
    http_response_code(200);
  } else {
    http_response_code(500);
  }
?>