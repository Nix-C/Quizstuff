<?php
  include 'config/database.php';
  // $env = parse_ini_file('../.env');
  // $CAPTCHA_KEY = $env['CAPTCHA_KEY'];
  $page_title = "Order Form";

  // Get products from database
  $sql_products = "SELECT * FROM products"; //select all from table name
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

  if(count($ids_with_variants) > 0) {
    $placeholders = implode(',', array_fill(0, count($ids_with_variants), '?'));
    $query = "SELECT * FROM product_variants WHERE product_id IN ($placeholders)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param(str_repeat('i', count($ids_with_variants)), ...$ids_with_variants);
    $stmt->execute();
    $result_variants = $stmt->get_result();
    $stmt->close();
  
    $product_variants = $result_variants->fetch_all(MYSQLI_ASSOC);
  } else {
    $product_variants = null;
  }

  // Get product options
  $with_options = array_filter($products, function($product) {
    return $product['has_options'];
  });
  
  // Extract product ids
  $ids_with_options = array_map(function($product) {
    return $product['id'];
  }, $with_options);
  if(!empty($ids_with_options)){
    $placeholders = implode(',', array_fill(0, count($ids_with_options), '?'));
    $query = "SELECT * FROM product_options WHERE product_id IN ($placeholders)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param(str_repeat('i', count($ids_with_options)), ...$ids_with_options);
    $stmt->execute();
    $result_options = $stmt->get_result();
    $stmt->close();

    $product_options = $result_options->fetch_all(MYSQLI_ASSOC);
  } else {
    $product_options = null;
  }
 
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
        <form id="order-form">
          <h2>Customer Information</h2>
          <div class="info--wrapper">
            <fieldset class="above customer-info">
              <!-- <legend>Name</legend> -->
              <label for="name-first">First Name
                <input type="text" name="name-first" id="name-first" required>
              </label>
              <label for="name-last">Last Name
                <input type="text" name="name-last" id="name-last" required>
              </label>
            </fieldset>
            <fieldset class="above customer-info">
              <!-- <legend>Shipping Information</legend> -->
              <label>Address Line 1
                <input type="text" name="address-1" id="address-1" required>
              </label>
              <label>Address Line 2
                <input type="text" name="address-2" id="address-2">
              </label>
              <label for="city">City
                <input type="text" name="city" id="city" required>
              </label>
              <label for="state">State
              <select name="state" id="state">
                <option value="" disabled selected>Select a state</option>
                <option value="AL">AL</option>
                <option value="AK">AK</option>
                <option value="AZ">AZ</option>
                <option value="AR">AR</option>
                <option value="CA">CA</option>
                <option value="CO">CO</option>
                <option value="CT">CT</option>
                <option value="DE">DE</option>
                <option value="FL">FL</option>
                <option value="GA">GA</option>
                <option value="HI">HI</option>
                <option value="ID">ID</option>
                <option value="IL">IL</option>
                <option value="IN">IN</option>
                <option value="IA">IA</option>
                <option value="KS">KS</option>
                <option value="KY">KY</option>
                <option value="LA">LA</option>
                <option value="ME">ME</option>
                <option value="MD">MD</option>
                <option value="MA">MA</option>
                <option value="MI">MI</option>
                <option value="MN">MN</option>
                <option value="MS">MS</option>
                <option value="MO">MO</option>
                <option value="MT">MT</option>
                <option value="NE">NE</option>
                <option value="NV">NV</option>
                <option value="NH">NH</option>
                <option value="NJ">NJ</option>
                <option value="NM">NM</option>
                <option value="NY">NY</option>
                <option value="NC">NC</option>
                <option value="ND">ND</option>
                <option value="OH">OH</option>
                <option value="OK">OK</option>
                <option value="OR">OR</option>
                <option value="PA">PA</option>
                <option value="RI">RI</option>
                <option value="SC">SC</option>
                <option value="SD">SD</option>
                <option value="TN">TN</option>
                <option value="TX">TX</option>
                <option value="UT">UT</option>
                <option value="VT">VT</option>
                <option value="VA">VA</option>
                <option value="WA">WA</option>
                <option value="WV">WV</option>
                <option value="WI">WI</option>
                <option value="WY">WY</option>
              </select>
              </label>
              <label for="zip">Zip Code
                <input type="text" name="zip" id="zip" required>
              </label>
            </fieldset>
            <fieldset class="above customer-info">
              <!-- <legend>Contact</legend> -->
              <label for="email">Email
                <input type="email" name="email" id="email" required>
              </label>
              <label for="phone">Phone
                <input type="tel" name="phone" id="phone" required>
              </label>
            </fieldset>
          </div>
          <h2>Products</h2>
          <?php foreach($products as $product) : ?>
            <fieldset class="form--product">
              <legend><?= $product['name'] ?></legend>
              <p><?= nl2br($product['description']) ?></p>
              <?php if(!$product['has_variants']) : ?>
                <span>Price: $<?= $product['price'] ?> ea.</span>
              <?php endif; ?>
              <?php if($product['image']) : ?>
                <img src="<?= $product['image'] ?>" alt="<?= $product['name'] ?>"> 
              <?php endif; ?>

              <?php if($product['has_variants']) : ?>
                <?php foreach($product_variants as $variant) : ?>
                  <?php if($product['id'] == $variant['product_id']) : ?>
                    <fieldset>
                      <legend><?= $variant['name'] ?></legend>
                      <span class="full-row">Price $<?= $variant['price'] ? $variant['price'] : $product['price'] ?> ea.</span>
                      <label class="qty--label">Quantity:
                        <input 
                          type="number"  
                          id="variant_<?= $product['id'] ?>_<?= $variant['id'] ?>" 
                          name="variant_<?= $product['id'] ?>_<?= $variant['id'] ?>" 
                          min="0" max="100" step="1" value="0"
                        >
                        <button 
                          type="button"
                          class="button--input button"
                          onclick="decrementQty('variant_<?= $product['id'] ?>_<?= $variant['id']?>')"
                        >-</button>
                        <button 
                          type="button" 
                          class="button button--input"
                          onclick="incrementQty('variant_<?= $product['id'] ?>_<?= $variant['id']?>')"
                        >+</button>
                      </label> 
                    </fieldset>
                  <?php endif; ?>
                <?php endforeach; ?>
              <?php else: ?>
                <?php if($product['has_options']) : ?>
                  <fieldset class="full-row">
                    <legend>Add-ons</legend>
                    <?php foreach($product_options as $option) : ?>
                      <?php if($product['id'] == $option['product_id']) : ?>
                        <label for="option_<?= $product['id'] ?>_<?= $option['id'] ?>"><?= $option['name'] ?>
                          <input 
                            type="checkbox" 
                            id="option_<?= $product['id'] ?>_<?= $option['id'] ?>"
                            name="option_<?= $product['id'] ?>_<?= $option['id'] ?>" 
                            value="true"
                          >
                          <span>+$<?= $option['price'] ?> ea.</span>
                        </label>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </fieldset>
                <?php endif; ?>
                <label class="qty--label full-row">Quantity:
                  <input 
                  type="number" 
                  id="product_<?= $product['id'] ?>" 
                  name="product_<?= $product['id'] ?>" 
                  min="0" max="100" step="1" value="0"
                  >
                  <button 
                        type="button"
                        class="button--input button"
                        onclick="decrementQty('product_<?= $product['id'] ?>')"
                  >-</button>                     
                  <button 
                    type="button" 
                    class="button button--input"
                    onclick="incrementQty('product_<?= $product['id'] ?>')"
                  >+</button>                    
                </label>
                
              <?php endif; ?>

              <!-- <button action="add" name="add">Add to Cart</button> -->
            </fieldset>
          <?php endforeach; ?>
          <p style="color:#bbd7e5"><strong>** Shipping costs will be added to the total when your order is confirmed. Your updated total will be sent after the initial order confirmation email.</strong></p>
          <div id="agreement" style="display: flex; gap: 10px; margin-top: 20px;">
            <input type="checkbox" name="agree" id="agree" required>
            <p>I agree to make payment by check after I have received an updated invoice with tax applied.</p>
          </div>
          <button type="submit" id="button--submit" class="button" name="submit">Submit Order</button>
          <!-- <p id="est-total">Estimated Total: $<span id="est-total--value">0.00</span></p>
          <small>(Pre shipping) -->
          <p id="submit-message"></p>
        </form>
      </section>
    </main>

    <?php include '../footer.php'; ?>
  </body>
  <script src="order-form.js"></script>
</html>
