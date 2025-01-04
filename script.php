<?php

    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;

    require_once 'PHPMailer/src/Exception.php';
    //require_once 'PHPMailer/src/PHPMailer.php';
    //require_once 'PHPMailer/src/SMTP.php';
    require_once '/usr/share/php/libphp-phpmailer/src/PHPMailer.php';
    require_once '/usr/share/php/libphp-phpmailer/src/SMTP.php';

    require 'config.php';

    function sendMail($to, $subject, $txt){

        // Create a new mail instance
        $mail = new \PHPMailer\PHPMailer\PHPMailer();   

        // Send using SMTP Protocol
        $mail->isSMTP();

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
        $mail->Port = 465;

        // From
        $mail->setFrom(SEND_FROM, SEND_FROM_NAME);

        // To
        $mail->addAddress($to);

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

        // Send the email
        if(!$mail->send()){
            return "Failed to submit form. Please try again.";
        } else {
            return "Success.";
        }

    }

    //sendMail($from, $subject2, $txt2);

?>
