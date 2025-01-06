<?php

require 'script.php';

include 'store/config/database.php';



////////////////////////////////////////////////////////////////
// Access Database



          // Get products from database
          $sql_products = "SELECT * FROM products";
          $result = mysqli_query($conn, $sql_products);
          $products = mysqli_fetch_all($result, MYSQLI_ASSOC);

          // Get product variants
          $with_variants = array_filter($products, function($product) {
            return $product['has_variants'];
          });
        
          // Extract product ids
          $ids_with_variants = array_map(function($product) {
            return $product['id'];
          }, $with_variants);
        
          $placeholders = implode(',', array_fill(0, count($ids_with_variants), '?')); // I don't entirely understand this yet
          $query = "SELECT * FROM product_variants WHERE product_id IN ($placeholders)";
          $stmt = $conn->prepare($query);
          $stmt->bind_param(str_repeat('i', count($ids_with_variants)), ...$ids_with_variants);
          $stmt->execute();
          $result_variants = $stmt->get_result();
          $stmt->close();
        
          $product_variants = $result_variants->fetch_all(MYSQLI_ASSOC);
        
          // Get product options
          $with_options = array_filter($products, function($product) {
            return $product['has_options'];
          });

          // Extract product ids
          $ids_with_options = array_map(function($product) {
            return $product['id'];
          }, $with_options);
        
          $placeholders = implode(',', array_fill(0, count($ids_with_options), '?'));
          $query = "SELECT * FROM product_options WHERE product_id IN ($placeholders)";
          $stmt = $conn->prepare($query);
          $stmt->bind_param(str_repeat('i', count($ids_with_options)), ...$ids_with_options);
          $stmt->execute();
          $result_options = $stmt->get_result();
          $stmt->close();
        
          $product_options = $result_options->fetch_all(MYSQLI_ASSOC);



  ////////////////////////////////////////////////////////////////////////
  // PHP Emailng and Order Management


  // On Submit...
  if (isset($_POST['submit'])) {

    // Check if fields are empty
    if( empty($_POST['name-first']) || 
        empty($_POST['name-last']) || 
        empty($_POST['email']) || 
        empty($_POST['phone']) ||
        empty($_POST['address']) ||
        empty($_POST['city']) ||
        empty($_POST['state']) ||
        empty($_POST['zip'])
        //empty($_POST['quantity_1']) ||
        //empty($_POST['quantity_2']) ||
        //empty($_POST['quantity_3']) ||
        //empty($_POST['quantity_4']) ||
        //empty($_POST['option_3'])
      ){

        $response = "All fields are required.";

        echo $response;

      } else {

        //Fields are not empty... get data

        $fname = $_POST['name-first'];

        $lname = $_POST['name-last'];

        $address = $_POST['address'];

        $city = $_POST['city'];

        $state = $_POST['state'];

        $zip = $_POST['zip'];

        $from = $_POST['email'];

        $phone = $_POST['phone'];


        // ↓↓↓ Need logic to get quantity of products ↓↓↓

        ////////////////////////////////////////////////////////////////////////////////////////////////
        // Getting quantity of products

                    // Initialize arrays to store product and option quantities and total price
                    $quantities = [];
                    $total_price = 0;

                    // Start building HTML invoice
                    $invoice = '<h1>Thank you for shopping at Quizstuff.com</h1>';
                    $invoice .= "<h3>Your order confirmation is below.</h3>";
                    $invoice .= '<table border="1" cellspacing="0" cellpadding="5">';
                    $invoice .= '<tr><th>Item</th><th>Quantity</th><th>Price</th><th>Total</th></tr>';

                    foreach ($products as $product) {
                        $product_id = $product['id'];
                        $product_name = $product['name']; // Get product name
                        $product_price = $product['price']; // Get product price
                    
                        // Initialize quantity for this product
                        if (!isset($quantities[$product_id])) {
                            $quantities[$product_id] = 0;
                        }
                      
                        // Process product options as separate products
                        if ($product['has_options']) {
                            foreach ($product_options as $option) {
                                if ($product_id == $option['product_id']) {
                                    $option_id = $product_id . '_option_' . $option['id']; // Treat option as separate product
                                    $option_name = $option['name']; // Get option name
                                    $option_price = $option['price']; // Get option price
                                
                                    if (!isset($quantities[$option_id])) {
                                        $quantities[$option_id] = 0;
                                    }
                                    $key = 'option_' . $option_name; // Match input name to option name
                                    if (isset($_POST[$key])) {
                                        $quantity = intval($_POST[$key]);
                                        $quantities[$option_id] += $quantity; // Add option quantity
                                        $total_price += $quantity * $option_price; // Add to total price
                                    
                                        // Add option to invoice
                                        $invoice .= '<tr><td>' . htmlspecialchars($option_name) . '</td><td>' . $quantity . '</td><td>$' . number_format($option_price, 2) . '</td><td>$' . number_format($quantity * $option_price, 2) . '</td></tr>';
                                    }
                                }
                            }
                        }
                      
                        // Process product variants
                        elseif ($product['has_variants']) {
                            foreach ($product_variants as $variant) {
                                if ($product_id == $variant['product_id']) {
                                  $key = 'quantity_' . $product_name; // Match input name to product name
                                    if (isset($_POST[$key])) {
                                        $quantity = intval($_POST[$key]);
                                        $quantities[$product_id] += $quantity; // Add variant quantity
                                        $total_price += $quantity * $product_price; // Add to total price
                                    
                                        // Add variant to invoice
                                        $invoice .= '<tr><td>' . htmlspecialchars($product_name) . '</td><td>' . $quantity . '</td><td>$' . number_format($product_price, 2) . '</td><td>$' . number_format($quantity * $product_price, 2) . '</td></tr>';
                                    }
                                }
                            }
                        } 
                      
                        // Process default quantity for products without options/variants
                        else {
                          $key = 'quantity_' . $product_name; // Match input name to product name
                            if (isset($_POST[$key])) {
                                $quantity = intval($_POST[$key]);
                                $quantities[$product_id] += $quantity;
                                $total_price += $quantity * $product_price; // Add to total price
                            
                                // Add product to invoice
                                $invoice .= '<tr><td>' . htmlspecialchars($product_name) . '</td><td>' . $quantity . '</td><td>$' . number_format($product_price, 2) . '</td><td>$' . number_format($quantity * $product_price, 2) . '</td></tr>';
                            }
                        }
                    }

                    // Close the table and add total price
                    $invoice .= '<tr><td colspan="3" align="right"><strong>Total:</strong></td><td><strong>$' . number_format($total_price, 2) . '</strong></td></tr>';
                    $invoice .= '</table>';

                    // Output invoice
                    echo $invoice;




        // Declare email data
        $to = "quizstuff@quizstuff.com";

        $subject = "Quizstuff Form Submission";

        $subject2 = "Your Order Has Been Placed";

        $headers = "From: " . $from;

        $headers2 = "From: " . $to;

        $txt = $fname . " " . $lname . " has submitted a form on Quizstuff.com. \n ******************************************************************** \n\n" . "Phone: " . $phone . "\n" . "Email: " . $from . "\n" . "Address: " . $address . "\n" . "City: " . $city . "\n" . "State: " . $state . "\n" . "Zip: " . $zip . "\n\n" /* . "Quiz Boxes: " . $qb . "\n" . "Pads: " . $pads . "\n" . "Quiz Machine (Software): " . $qm . "\n" . "Quiz Machine (Software) with Lifetime Upgrades: " . $qm_w_upgrades . "\n" . "Quiz Machine (5 Users): " . $qm_5_usr . "\n" */;



        // Invoice
        $txt2 = $invoice;
        // $txt2 = "
        //   <body>
        //     <h1> I am the invoice </h1>

        //     <p>" . "Email: " . $from . "\n" . 
        //       "Phone: " . $phone . 
        //       "Address: " . $address . "\n" . 
        //       "City: " . $city . "\n" . 
        //       "State: " . $state . "\n" . 
        //       "Zip: " . $zip . "\n\n " . 
        //     "</p>
            
        //     <p> <b>Your total is: $</b> <span>" . number_format($total) . "</span> </p>
        //   </body>
        // ";


          // Send data to quizstuff@quizstuff.com
          sendMail($to, $subject, $txt, $headers);

          // Send invoice to client
          sendMail($from, $subject2, $txt2, $headers2);
          
          // Redirect to homepage after sending emails
          header("Location: https://dev.quizstuff.com");
          
          //$response = "Thank you, " . $fname . " We will contact you shortly.\n Please check your email for your order confirmation and invoice.";
      }
  }
  else {
    // Do not allow user to submit a form without pressing the submit button
    echo "Submit button is not set";
  }
 
?>
