<?php
  // Page variables
  $page_title = "Order Form";

  // Spoof DB 🤡
  // Product class
  class product {
    public $name;
    public $price;
    public $description;
    public $image;
    public $id;

    public function __construct($name, $price, $description, $image, $id) {
      $this->name = $name;
      $this->price = $price;
      $this->description = $description;
      $this->image = $image;
      $this->id = $id;
    }
  }


  // Array of 5 products
  $products = [
    new product('Chair Pads', 75.00, 'A chair pad', '', 1),
    new product('Laptop', 215, 'A refurbished laptop with Quizstuff installed.', '', 2),
    new product('USB Interface', 135.00, 'ProductDescription', '', 3),
    new product('QuizMachine', 50, 'ProductDescription', '', 4),
    new product('QuizMachine DQD', 75, 'ProductDescription', '', 5)
  ];
?>

<!DOCTYPE html>
<html lang="en">
  <?php include '../head.php'; ?>
  <body>
    <div id="canvas">
      <div id="radial-1"></div>
      <div id="radial-2"></div>
    </div>
    <?php include '../header.php'; ?>
    <main>
      <section class="container">
      <h1>Order Form</h1>
        <form>
          <?php 
            foreach ($products as $product) {
              echo "<fieldset>";
              echo "<legend>$product->name</legend>";
              echo "<label>Price: $$product->price</label>";
              // echo "<label>Description: $product->description</label>";
              echo "<label>Quantity: <input type='number' name='quantity' id='quantity' min='0' max='100' steps='1' value='0'></label>";
              echo "</fieldset>";
            }
          ?>
          <button>Submit Order</button>
        </form>
      </section>
    </main>

    <?php include '../footer.php'; ?>
  </body>
</html>
