<?php

include 'generate-invoice.php';

function sendOrderInvoice($invoice){

    //Use HTML Variable
    ob_start();
?>



<body>
    <h1>Quizstuff Invoice</h1>
    <?php $invoice ?>
</body>



<?php

    //This is the name of the HTML Variable
    $userEmail = ob_get_clean();

}

?>