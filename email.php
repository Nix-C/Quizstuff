<?php

require 'script.php';

  if (isset($_POST['submit'])) {

    if( empty($_POST['firstname']) || 
        empty($_POST['lastname']) || 
        empty($_POST['email']) || 
        empty($_POST['phone']) ||
        empty($_POST['address']) ||
        empty($_POST['city']) ||
        empty($_POST['state']) ||
        empty($_POST['zip']) ||
        empty($_POST['quantity_1']) ||
        empty($_POST['quantity_2']) ||
        empty($_POST['quantity_3']) ||
        empty($_POST['quantity_4']) ||
        empty($_POST['option_3'])
      ){

        $response = "All fields are required.";

      } else {

        $fname = $_POST['name-first'];

        $lname = $_POST['name-last'];

        $address = $_POST['address'];

        $phone = $_POST['city'];

        $state = $_POST['state'];

        $zip = $_POST['zip'];

        $from = $_POST['email'];

        $phone = $_POST['phone'];

        $qb = $_POST['quantity_1'];

        $pads = $_POST['quantity_2'];

        $qm = $_POST['quantity_3'];

        $qm_w_upgrades = $_POST['option_3'];

        $qm_5_usr = $_POST['quantity_4'];




        $to = "quizstuff@quizstuff.com";

        $subject = "Quizstuff Form Submission";

        $subject2 = "Your Order Has Been Placed";

        $headers = "From: " . $from;

        $headers2 = "From: " . $to;

        $txt = $fname . " " . $lname . " has submitted a form on Quizstuff.com. \n ******************************************************************** \n\n" . "Phone: " . $phone . "\n" . "Email: " . $from . "\n" . "Address: " . $address . "\n" . "City: " . $city . "\n" . "State: " . $state . "\n" . "Zip: " . $zip . "\n\n" . "Quiz Boxes: " . $qb . "\n" . "Pads: " . $pads . "\n" . "Quiz Machine (Software): " . $qm . "\n" . "Quiz Machine (Software) with Lifetime Upgrades: " . $qm_w_upgrades . "\n" . "Quiz Machine (5 Users): " . $qm_5_usr . "\n";

        $txt2 = "HTML INVOICE GOES HERE";



          sendMail($to, $subject, $txt, $headers);

          sendMail($from, $subject2, $txt2, $headers2);

          $response = "Thank you, " . $fname . "We will contact you shortly.\n Please check your email for your order confirmation and invoice.";

          header("Location https://dev.quizstuff.com");
      }
  }
  else {
    echo "Submit button is not set";
  }
 
?>
