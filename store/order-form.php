<?php
  include 'config/database.php';
  $page_title = "Order Form";

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
      <p>Fill out the form below to place an order.</p>
        <form id="order-form">
          <h2>Customer Information</h2>
          <fieldset>
            <legend>Name</legend>
            <label for="name-first">First Name:
              <input type="text" name="name-first" id="name-first" required>
            </label>
            <label for="name-last">Last Name:
              <input type="text" name="name-last" id="name-last" required>
            </label>
          </fieldset>
          <fieldset>
            <legend>Shipping Information</legend>
            <label>Street Address:
              <input type="text" name="address-1" id="address-1" required>
              <input type="text" name="address-2" id="address-2">
            </label>
            <label for="city">City:
              <input type="text" name="city" id="city" required>
            </label>
            <label for="state">State:
              <input type="text" name="state" id="state" required>
            </label>
            <label for="zip">Zip Code:
              <input type="text" name="zip" id="zip" required>
            </label>
          </fieldset>
          <fieldset>
            <legend>Contact</legend>
            <label for="email">Email:
              <input type="email" name="email" id="email" required>
            </label>
            <label for="phone">Phone:
              <input type="tel" name="phone" id="phone" required>
            </label>
          </fieldset>
          <h2>Products</h2>
          <?php foreach($products as $product) : ?>
            <fieldset>
              <legend><?= $product['name'] ?></legend>
              <p><?= $product['description'] ?></p>
              <?php if(!$product['has_variants']) : ?>
                <span>Price: $<?= $product['price'] ?></span>
              <?php endif; ?>
              <?php if($product['image']) : ?>
                <img src="<?= $product['image'] ?>" alt="<?= $product['name'] ?>"> 
              <?php endif; ?>

              <?php if($product['has_variants']) : ?>
                <?php foreach($product_variants as $variant) : ?>
                  <?php if($product['id'] == $variant['product_id']) : ?>
                    <fieldset>
                      <legend><?= $variant['name'] ?></legend>
                    <label>Quantity: 
                      <input 
                        type="number"  
                        id="variant_<?= $product['id'] ?>_<?= $variant['id'] ?>" 
                        name="variant_<?= $product['id'] ?>_<?= $variant['id'] ?>" 
                        min="0" max="100" step="1" value="0"
                      >
                    </label> <span>Price: $<?= $variant['price'] ? $variant['price'] : $product['price'] ?></span>
                  </fieldset>
                  <?php endif; ?>
                <?php endforeach; ?>
              <?php else: ?>
                <label>Quantity: 
                  <input 
                    type="number" 
                    id="product_<?= $product['id'] ?>" 
                    name="product_<?= $product['id'] ?>" 
                    min="0" max="100" step="1" value="0"
                  ></label>
                <?php if($product['has_options']) : ?>
                  <fieldset>
                    <legend>Options</legend>
                    <?php foreach($product_options as $option) : ?>
                      <?php if($product['id'] == $option['product_id']) : ?>
                        <label for="option_<?= $product['id'] ?>_<?= $option['id'] ?>"><?= $option['name'] ?>
                          <input 
                            type="checkbox" 
                            id="option_<?= $product['id'] ?>_<?= $option['id'] ?>"
                            name="option_<?= $product['id'] ?>_<?= $option['id'] ?>" 
                            value="true"
                          >
                          <span>+$<?= $option['price'] ?></span>
                        </label>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </fieldset>
                <?php endif; ?>
              <?php endif; ?>

              <!-- <button action="add" name="add">Add to Cart</button> -->
            </fieldset>
          <?php endforeach; ?>
          <button type="submit" name="submit">Submit Order</button>
          <p id="submit-message"></p>
        </form>
      </section>
    </main>

    <?php include '../footer.php'; ?>
  </body>
  <script src="order-form.js"></script>
</html>
