<?php
  require_once '../script.php';
  require 'generate-invoices.php';
  require 'process-order.php';

  function sendUserInvoice($invoice, $orderId, $userEmail){
    $currentYear = date("Y");
    $subject = "Quizstuff Invoice ID#${orderId}";
    //Use HTML Variable
    ob_start();
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quizstuff Invoice</title>
</head>
<body style="margin: 0; padding: 20px; background: radial-gradient(circle at top left, #004b78 5%, transparent), radial-gradient(circle at bottom right, #431235 5%, rgb(13, 18, 28)); font-family: Arial, sans-serif; color: #ffffff; line-height: 1.5; border: 8px solid transparent;">
    <img src="https://dev.quizstuff.com/assets/images/ostritch_final_1.png" alt="Logo" style="position: absolute; top: 10px; left: 10px; width: 80px; height: auto; max-width: 100%; max-height: 100%; border-radius: 4px;">
    <div style="width: 100%; max-width: 600px; margin: 20px auto; background-color: #1E1E1E; border: 1px solid #333; border-radius: 8px; overflow: hidden; position: relative;">
        <!-- Header -->
        <div style="background-color: #007BFF; color: #ffffff; padding: 20px; position: relative;">
            <div style="text-align: center; font-size: 24px; font-weight: bold;">Quizstuff Invoice</div>
        </div>
        <!-- Body -->
        <div style="padding: 20px;">
            <p style="margin: 0 0 20px;">Thank you for your order. Please find your invoice details below:</p>
            
            
            <!-- $invoice -->
            <?= $invoice ?>
            <p>Order ID#<?= $orderId ?>
            <!-- Payment instructions -->
            <p style="margin: 0 0 10px;">Please make the payment by check. We will contact you with more payment information. <br> If you have any questions, feel free to contact us at <strong>quizstuff@quizstuff.com</strong>.</p>
        </div>
        <!-- Footer -->
        <div style="background-color: #121212; color: #aaaaaa; padding: 10px; text-align: center; font-size: 12px;">
            &copy; <?= $currentYear ?> Quizstuff.com. All rights reserved.
        </div>
    </div>
</body>
</html>

<?php
    //This is the name of the HTML Variable
    $emailContent = ob_get_clean();

    // 🚧 For testing, remove when done. 🚧
    //echo $emailContent;
    
    if(
      // TODO: Replace true with sendmail
      sendMail($userEmail, $subject, $emailContent)
    ) {
      return true;
      sendMail($userEmail, $subject, $emailContent);
    }

  }
  // 🚧 For testing, remove when done. 🚧
  //include 'generate-invoice.php';
  //$invoice = generateInvoice(1);
  //echo sendUserInvoice($invoice, 1, "nixc.web@gmail.com");
?>