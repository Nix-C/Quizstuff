<?php

  if (isset($_POST['submit'])) {

    if( empty($_POST['firstname']) || 
        empty($_POST['lastname']) || 
        empty($_POST['email']) || 
        empty($_POST['phone'])
      ){

        $response = "All fields are required.";

      } else {

        $fname = $_POST['firstname'];

        $lname = $_POST['lastname'];

        $from = $_POST['email'];

        $phone = $_POST['phone'];




        $to = "quizstuff@quizstuff.com";

        $subject = "Quizstuff Form Submission";

        $subject2 = "Your Order Has Been Placed";

        $headers = "From: " . $from;

        $headers2 = "From: " . $to;

        $txt = $fname . " " . $lname . " has submitted a form on Quizstuff.com. \n ******************************************************************** \n\n" . "Phone: " . $phone . "\n\n" . "Email: " . $from . "\n";

        $txt2 = "HTML INVOICE GOES HERE";



          sendMail($to, $subject, $txt, $headers);

          sendMail($from, $subject2, $txt2, $headers2);

          $response = "Thank you, " . $fname . "We will contact you shortly.\n Please check your email for your order confirmation and invoice.";

          // header("Location http://msparenti.com/CCDCP/index.html");
      }
  }
 
?>
