<?php
  include 'config/database.php';
  $page_title = "Equipment Registration";
  $error = '';
  $success = '';

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Backend validation
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $district = trim($_POST['district'] ?? '');
    $agree = isset($_POST['agree']);

    if ($first_name === '' || $last_name === '' || $phone === '' || $email === '' || $district === '' || !$agree) {
      $error = "All fields are required and you must agree to the terms.";
    } else {
      // Insert into equipment_registration table
      $stmt = $conn->prepare("INSERT INTO equipment_registration (first_name, last_name, phone, email, district) VALUES (?, ?, ?, ?, ?)");
      $stmt->bind_param('sssss', $first_name, $last_name, $phone, $email, $district);
      if ($stmt->execute()) {
        $success = "Equipment registered successfully.";
        $registration_id = $conn->insert_id;
        // Convert empty string integer fields to null
        function int_or_null($val) {
          return ($val === '' || $val === null) ? null : (int)$val;
        }
        // Assign int_or_null results to variables to avoid 'only variables should be passed by reference' notice
        $interface_qty = int_or_null($_POST['interface_qty']);
        $projector_lumens = int_or_null($_POST['projector_lumens']);
        $projector_qty = int_or_null($_POST['projector_qty']);
        $powerstrip_outlets = int_or_null($_POST['powerstrip_outlets']);
        $extension_length = int_or_null($_POST['extension_length']);
        $mic_qty = int_or_null($_POST['mic_qty']);
        $other_qty = int_or_null($_POST['other_qty']);
        // Insert into equipment_details table (excluding pads)
        $stmt2 = $conn->prepare("INSERT INTO equipment_details (
          registration_id,
          laptop_brand, laptop_os, laptop_parallel_port, laptop_qm_version, laptop_username, laptop_password,
          interface_type, interface_qty,
          monitor_brand, monitor_size, monitor_resolution,
          projector_brand, projector_lumens, projector_resolution, projector_qty,
          powerstrip_make, powerstrip_model, powerstrip_color, powerstrip_outlets,
          extension_color, extension_length,
          mic_type, mic_brand, mic_model, mic_qty,
          other_desc, other_qty
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)" );
        $stmt2->bind_param(
          'issssssissssssissssssisssssi',
          $registration_id,
          $_POST['laptop_brand'],
          $_POST['laptop_os'],
          $_POST['laptop_parallel_port'],
          $_POST['laptop_qm_version'],
          $_POST['laptop_username'],
          $_POST['laptop_password'],
          $_POST['interface_type'],
          $interface_qty,
          $_POST['monitor_brand'],
          $_POST['monitor_size'],
          $_POST['monitor_resolution'],
          $_POST['projector_brand'],
          $projector_lumens,
          $_POST['projector_resolution'],
          $projector_qty,
          $_POST['powerstrip_make'],
          $_POST['powerstrip_model'],
          $_POST['powerstrip_color'],
          $powerstrip_outlets,
          $_POST['extension_color'],
          $extension_length,
          $_POST['mic_type'],
          $_POST['mic_brand'],
          $_POST['mic_model'],
          $mic_qty,
          $_POST['other_desc'],
          $other_qty
        );
        $stmt2->execute();
        $stmt2->close();

        // Insert multiple pads
        if (!empty($_POST['pad_color']) && is_array($_POST['pad_color'])) {
          $pad_colors = $_POST['pad_color'];
          $pad_qtys = $_POST['pad_qty'];
          $pad_stmt = $conn->prepare("INSERT INTO pads (registration_id, pad_color, pad_qty) VALUES (?, ?, ?)");
          for ($i = 0; $i < count($pad_colors); $i++) {
            $color = $pad_colors[$i];
            $qty = int_or_null($pad_qtys[$i]);
            if ($color !== '' && $qty !== null) {
              $pad_stmt->bind_param('isi', $registration_id, $color, $qty);
              $pad_stmt->execute();
            }
          }
          $pad_stmt->close();
        }
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
            <h1>Personal Info</h1>
            <label for="first_name">First Name
              <input type="text" name="first_name" id="first_name" required value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>">
            </label>
            <label for="last_name">Last Name
              <input type="text" name="last_name" id="last_name" required value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>">
            </label>
            <label for="phone">Phone
              <input type="tel" name="phone" id="phone" required value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
            </label>
            <label for="email">Email
              <input type="email" name="email" id="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </label>
            <label for="district">District/Region
              <input type="text" name="district" id="district" required value="<?= htmlspecialchars($_POST['district'] ?? '') ?>">
            </label>
          </fieldset>

          <h1>Equipment</h1>
          <fieldset>
            <legend>Laptop</legend>
            <label>Brand: <input type="text" name="laptop_brand" placeholder="e.g. Dell"></label>
            <label>Operating System:
              <select name="laptop_os">
                <option value="">Select</option>
                <option value="Win11+">Win11+</option>
                <option value="Win10">Win10</option>
                <option value="Win7/8">Win7/8</option>
                <option value="older than Win7">older than Win7</option>
                <option value="Linux">Linux</option>
              </select>
            </label>
            <label>Parallel Port Type:
              <select name="laptop_parallel_port">
                <option value="">Select</option>
                <option value="None">None</option>
                <option value="Built-in">Built-in</option>
                <option value="PCMCIA">PCMCIA</option>
                <option value="USB Adapter">USB Adapter</option>
                <option value="Other">Other</option>
              </select>
            </label>
            <label>Quizmachine Version: <input type="text" name="laptop_qm_version" placeholder="e.g. 2.1.0"></label>
            <label>Username: <input type="text" name="laptop_username" placeholder="e.g. admin"></label>
            <label>Password: <input type="password" name="laptop_password" placeholder="Password"></label>
          </fieldset>

          <fieldset>
            <legend>Interface Box</legend>
            <label>Type:
              <select name="interface_type">
                <option value="">Select</option>
                <option value="USB">USB</option>
                <option value="Parallel">Parallel</option>
              </select>
            </label>
            <label>Quantity: <input type="number" name="interface_qty" min="0" placeholder="e.g. 1"></label>
          </fieldset>

          <fieldset>
            <legend>Pads</legend>
            <div id="pads-container">
              <div class="pad-set">
                <label>Color:
                  <select name="pad_color[]">
                    <option value="">Select</option>
                    <option value="Red">Red</option>
                    <option value="Blue">Blue</option>
                    <option value="Green">Green</option>
                    <option value="Yellow">Yellow</option>
                  </select>
                </label>
                <label>Quantity: <input type="number" name="pad_qty[]" min="0" placeholder="e.g. 4"></label>
                <button type="button" class="remove-pad-set" style="display:none;">Remove</button>
              </div>
            </div>
            <button type="button" id="add-pad-set">Add Another Set of Pads</button>
          </fieldset>

          <fieldset>
            <legend>Monitor</legend>
            <label>Brand: <input type="text" name="monitor_brand" placeholder="e.g. Samsung"></label>
            <label>Size (inches):
              <select name="monitor_size">
                <option value="">Select</option>
                <option value="19">19"</option>
                <option value="21">21"</option>
                <option value="22">22"</option>
                <option value="24">24"</option>
                <option value="27">27"</option>
                <option value="32">32"</option>
                <option value="Other">Other</option>
              </select>
            </label>
            <label>Resolution: <input type="text" name="monitor_resolution" placeholder="e.g. 1920x1080"></label>
          </fieldset>

          <fieldset>
            <legend>Projector</legend>
            <label>Brand: <input type="text" name="projector_brand" placeholder="e.g. Epson"></label>
            <label>Lumens: <input type="number" name="projector_lumens" min="0" placeholder="e.g. 3000"></label>
            <label>Resolution:
              <select name="projector_resolution">
                <option value="">Select</option>
                <option value="800x600">800x600</option>
                <option value="1024x768">1024x768</option>
                <option value="1280x800">1280x800</option>
                <option value="1920x1080">1920x1080</option>
                <option value="3840x2160">3840x2160 (4K)</option>
                <option value="Other">Other</option>
              </select>
            </label>
            <label>Quantity: <input type="number" name="projector_qty" min="0" placeholder="e.g. 1"></label>
          </fieldset>

          <fieldset>
            <legend>Powerstrip</legend>
            <label>Make: <input type="text" name="powerstrip_make" placeholder="e.g. Belkin"></label>
            <label>Model: <input type="text" name="powerstrip_model" placeholder="e.g. F9H620-06"></label>
            <label>Color: <input type="text" name="powerstrip_color" placeholder="e.g. White"></label>
            <label>Number of Plugs: <input type="number" name="powerstrip_outlets" min="0" placeholder="e.g. 6"></label>
          </fieldset>

          <fieldset>
            <legend>Extension Cord</legend>
            <label>Color: <input type="text" name="extension_color" placeholder="e.g. Orange"></label>
            <label>Length (ft): <input type="number" name="extension_length" min="0" placeholder="e.g. 25"></label>
          </fieldset>

          <fieldset>
            <legend>Microphone/Recorder</legend>
            <label>Type: <input type="text" name="mic_type" placeholder="e.g. Handheld, Tabletop"></label>
            <label>Brand: <input type="text" name="mic_brand" placeholder="e.g. Shure"></label>
            <label>Model: <input type="text" name="mic_model" placeholder="e.g. SM58"></label>
            <label>Quantity: <input type="number" name="mic_qty" min="0" placeholder="e.g. 1"></label>
          </fieldset>

          <fieldset>
            <legend>Other</legend>
            <label>Description:
              <textarea name="other_desc" placeholder="Describe other equipment" rows="3"></textarea>
            </label>
            <label>Quantity: <input type="number" name="other_qty" min="0" placeholder="e.g. 1"></label>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
  const padsContainer = document.getElementById('pads-container');
  const addPadSetBtn = document.getElementById('add-pad-set');

  addPadSetBtn.addEventListener('click', function() {
    const firstPadSet = padsContainer.querySelector('.pad-set');
    const newPadSet = firstPadSet.cloneNode(true);
    // Clear values
    newPadSet.querySelector('select').value = '';
    newPadSet.querySelector('input').value = '';
    newPadSet.querySelector('.remove-pad-set').style.display = 'inline-block';
    padsContainer.appendChild(newPadSet);
    updateRemoveButtons();
  });

  function updateRemoveButtons() {
    const padSets = padsContainer.querySelectorAll('.pad-set');
    padSets.forEach((set, idx) => {
      const removeBtn = set.querySelector('.remove-pad-set');
      removeBtn.onclick = function() {
        if (padSets.length > 1) {
          set.remove();
          updateRemoveButtons();
        }
      };
      removeBtn.style.display = padSets.length > 1 ? 'inline-block' : 'none';
    });
  }
  updateRemoveButtons();
});
</script>
