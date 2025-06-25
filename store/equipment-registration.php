<?php
  include 'config/database.php';
  $page_title = "Equipment Registration";
  $error = '';
  $success = '';

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Backend validation
    $equipment_name = trim($_POST['equipment_name'] ?? '');
    $serial_number = trim($_POST['serial_number'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $owner = trim($_POST['owner'] ?? '');
    $agree = isset($_POST['agree']);

    if ($equipment_name === '' || $serial_number === '' || $location === '' || $owner === '' || !$agree) {
      $error = "All fields are required and you must agree to the terms.";
    } else {
      // Insert into equipment_registration table
      $stmt = $conn->prepare("INSERT INTO equipment_registration (equipment_name, serial_number, location, owner) VALUES (?, ?, ?, ?)");
      $stmt->bind_param('ssss', $equipment_name, $serial_number, $location, $owner);
      if ($stmt->execute()) {
        $success = "Equipment registered successfully.";
      } else {
        $error = "Database error: " . $conn->error;
      }
      $stmt->close();
    }
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
        <h1>Equipment Registration</h1>
        <?php if ($error): ?>
          <p style="color:red;"><strong><?= htmlspecialchars($error) ?></strong></p>
        <?php elseif ($success): ?>
          <p style="color:green;"><strong><?= htmlspecialchars($success) ?></strong></p>
        <?php endif; ?>
        <form id="equipment-registration-form" method="post">
          <fieldset class="above">
            <label for="equipment_name">Equipment Name
              <input type="text" name="equipment_name" id="equipment_name" required value="<?= htmlspecialchars($_POST['equipment_name'] ?? '') ?>">
            </label>
            <label for="serial_number">Serial Number
              <input type="text" name="serial_number" id="serial_number" required value="<?= htmlspecialchars($_POST['serial_number'] ?? '') ?>">
            </label>
            <label for="location">Location
              <input type="text" name="location" id="location" required value="<?= htmlspecialchars($_POST['location'] ?? '') ?>">
            </label>
            <label for="owner">Owner
              <input type="text" name="owner" id="owner" required value="<?= htmlspecialchars($_POST['owner'] ?? '') ?>">
            </label>
          </fieldset>
          <div id="agreement" style="display: flex; gap: 10px; margin-top: 20px;">
            <input type="checkbox" name="agree" id="agree" required>
            <p>I confirm that the above information is accurate and I am authorized to register this equipment.</p>
          </div>
          <button type="submit" id="button--submit" class="button" name="submit">Register Equipment</button>
        </form>
      </section>
    </main>
    <?php include '../footer.php'; ?>
  </body>
</html>
