<?php
  include 'config/database.php';
  $page_title = "Order Form";   // Page variables

  // Database connection
  $sql = "SELECT * FROM products";
  $result = mysqli_query($conn, $sql);
  $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
  // Spoof DB 🤡
  // Product class
  // class product {
  //   public $name;
  //   public $price;
  //   public $description;
  //   public $image;
  //   public $id;

  //   public function __construct($name, $price, $description, $image, $id) {
  //     $this->name = $name;
  //     $this->price = $price;
  //     $this->description = $description;
  //     $this->image = $image;
  //     $this->id = $id;
  //   }
  // }


  // Array of 5 products
  // $products = [
  //   new product('Chair Pads', 75.00, 'A chair pad', '', 1),
  //   new product('Laptop', 215, 'A refurbished laptop with Quizstuff installed.', '', 2),
  //   new product('USB Interface', 135.00, 'ProductDescription', '', 3),
  //   new product('QuizMachine', 50, 'ProductDescription', '', 4),
  //   new product('QuizMachine DQD', 75, 'ProductDescription', '', 5)
  // ];
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
        <form action="order.php" method="POST" enctype="multipart/form-data">
          <?php foreach($products as $product) : ?>
            <fieldset>
              <legend><?= $product['name'] ?></legend>
              <label>Price: $<?= $product['price'] ?></label>
              <label>Description: <?= $product['description'] ?></label>
              <img src="..<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
              <label>Quantity: <input type="number" name="quantity_<?= $product['id'] ?>" id="quantity_<?= $product['id'] ?>" min="0" max="100" steps="1" value="0"></label>
            </fieldset>
          <?php endforeach; ?>
          <button action="submit" name="submit">Submit Order</button>
        </form>
      </section>
    </main>

    <?php include '../footer.php'; ?>
  </body>
</html>
