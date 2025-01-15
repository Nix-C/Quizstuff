<?php
  function sendOrderInvoice($invoice){

    //Use HTML Variable
    ob_start();
  ?>
    <body>
        <h1>Quizstuff Invoice</h1>
        <?= $invoice ?>
    </body>
  <?php

  //This is the name of the HTML Variable
  $userEmail = ob_get_clean();
  // 🚧 For testing, remove when done. 🚧
  echo $userEmail;

}
  // 🚧 For testing, remove when done. 🚧
  include 'generate-invoice.php';
  $invoice = generateInvoice(1);
  echo sendOrderInvoice($invoice);

?>