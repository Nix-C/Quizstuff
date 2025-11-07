<?php 
  include 'config/database.php';
  $page_title = "Equipment Registration";

  # Get Events
  $sql_events = "SELECT * FROM events";
  $result = mysqli_query($conn, $sql_events);
  $events = mysqli_fetch_all($result, MYSQLI_ASSOC);

  
?>

<!DOCTYPE html>
<html>
  <?php include '../head.php'; ?>
  <link rel="stylesheet" href="registration.css">
  <script src="index.js" defer></script>
  <body>
    <?php include '../header.php'; ?>
    <main>
      <section class="container">
        <h1>Equipment Registration</h1>
        <form id="form">
          <!-- Event -->
          <fieldset>
            <legend>Event</legend>
            <label for="event">
              <select name="event" required>
                <option value="">Select Event</option>
                <?php foreach($events as $event) :?>
                  <option value="<?= $event["event_id"] ?>"><?= $event["name"] ?></option>
                <?php endforeach ?>
              </select>
            </label>
          </fieldset>
          
          <!-- Personal Info -->
          <fieldset class="above">
            <legend>Personal Information</legend>
            <label for="name-first">First Name
              <input type="text" name="name_first" id="name-first" required >
            </label>
            <label for="name-last">Last Name
              <input type="text" name="name_last" id="name-last" required >
            </label>
            <label for="district">District & Field
              <input type="text" name="district" id="district" required >
            </label>
            <label for="email">Email
              <input type="email" name="email" id="email" required >
            </label>
            <label for="phone">Phone
              <input type="tel" name="phone" id="phone" required >
            </label>
          </fieldset>
          <!-- Equipment -->
          <h2>Equipment</h2>
          <div>
            <h3>Laptops</h3>
            <!-- Laptop block (pre-added by js) -->
            <button id="add-laptop" data-type="laptop">+ Add Laptop</button>
          </div>
          <div>
            <h3>Pads</h3>
            <!-- Pads block -->
            <button id="add-pads" data-type="pads">+ Add Pads</button>
          </div>
          <div>
            <h3>Monitors</h3>
            <!-- Monitor block -->
            <button id="add-monitor" data-type="monitor">+ Add Monitor</button>
          </div>
          <div>
            <h3>Projectors</h3>
            <!-- Projector block -->
            <button id="add-projector" data-type="projector">+ Add Projector</button>
          </div>
          <div>
            <h3>Powerstrips</h3>
            <!-- Powerstrip block -->
            <button id="add-powerstrip" data-type="powerstrip">+ Add Powerstrip</button>
          </div>
          <div>
            <h3>Extension Cords</h3>
            <!-- Extension block -->
            <button id="add-extension" data-type="extension">+ Add Extension Cord</button>
          </div>
          <!-- <div>
            <h3>Microphones/Recorders</h3>
            
            <button id="add-mic" data-type="mic">+ Add Microphone/Recorder</button>
          </div> -->
          <div>
            <h3>Other Equipment</h3>
            <!-- other block -->
            <button id="add-other" data-type="other">+ Add Other Equipment</button>
          </div>


          <label id="agreement" style="display: flex; gap: 10px; margin-top: 20px;">
            <input type="checkbox" name="agree" id="agree" required >
            <p>I confirm that the above information is accurate and I am authorized to register this equipment.</p>
          </label>
          <!-- <div class="cf-turnstile" data-sitekey="0x4AAAAAAB7VJCAOCIRo1v9k"></div> -->
          <div class="cf-turnstile" data-sitekey="1x00000000000000000000AA"></div>
          
          <button type="submit" id="button--submit" class="button" name="submit">Register Equipment</button>
        </form>
      </section>

      <!-- TEMPLATES -->
      <div id="templates" style="display: none">
        <fieldset id="laptop-template" class="wrap">
          <!-- <legend>Laptop <span id="laptop-i">1</span></legend> -->
          <label>Brand
            <input type="text" maxlength="30" name="laptop-brand-?" required>
          </label>
          <label>Operating System
            <select name="laptop-operating_system-?" required>
              <option value="">Please Select</option>
              <option value="Win 11+">Windows 11 (or newer)</option>
              <option value="Win 10">Windows 10</option>
              <option value="Win 7/8">Windows 7/8</option>
              <option value="Older Windows">Older Windows</option>
              <option value="Linux">Linux</option>
              <option value="Other">Other</option>
            </select>
          </label>
          <label>Port Type
            <select name="laptop-port_type-?" required>
              <option value="">Please Select</option>
              <option value="None">None</option>
              <option value="Built-in">Built-in</option>
              <option value="PCMCIA">PCMCIA</option>
              <option value="USB Adapter">USB Adapter</option>
            </select>
          </label>
          <label>QuizMachine Version
            <input type="text" name="laptop-quizmachine_version-?" maxlength="15" required>
          </label>
          <label>Username
            <input type="text" name="laptop-username-?" maxlength="30" required>
          </label>
          <label>Password
            <input type="password" name="laptop-password-?" maxlength="30" required>
          </label>
        </fieldset>

        <fieldset id="pads-template">
          <!-- <legend>Pads <span>?</span></legend> -->
          <label>Color
            <select name="pads-color-?" required>
              <option value="">Please Select</option>
              <option value="Red">Red</option>
              <option value="Blue">Blue</option>
              <option value="Green">Green</option>
              <option value="Yellow">Yellow</option>
              <option value="Other">Other</option>
            </select>
          </label>
          <label>Quantity
            <input type="number" name="pads-qty-?" required>
          </label>
        </fieldset>

        <fieldset id="monitor-template">
          <!-- <legend>Monitor</legend> -->
          <label>Brand 
            <input type="text" name="monitor-brand-?" placeholder="e.g. Samsung" required>
          </label>
          <label>Size (inches):
            <select name="monitor-size-?" required>
              <option value="">Please Select</option>
              <option value="19">19"</option>
              <option value="21">21"</option>
              <option value="22">22"</option>
              <option value="24">24"</option>
              <option value="27">27"</option>
              <option value="32">32"</option>
              <option value="Other">Other</option>
            </select>
          </label>
          <label>Resolution
            <select name="monitor-resolution-?" required>
              <option value="">Please Select</option>
              <option value="HD (1080)">HD (1080)"</option>
              <option value="UHD (3840)">UHD (3840)"</option>
              <option value="Other">Other"</option>
            </select>
          </label>
          <label>Connection Type
            <select name="monitor-connection_type-?" required>
              <option value="">Please Select</option>
              <option value="HD (1080)">HD (1080)"</option>
              <option value="UHD (3840)">UHD (3840)"</option>
              <option value="Other">Other"</option>
            </select>                
          </label>
        </fieldset>

        <fieldset id="projector-template">
          <!-- <legend>Projector</legend> -->
          <label>Brand 
            <input type="text" name="projector-brand-?" placeholder="e.g. Epson" required>
          </label>
          <label>Lumens
            <input type="number" name="projector-lumens-?" min="0" placeholder="e.g. 3000" required>
          </label>
          <label>Resolution:
            <select name="projector-resolution-?" required>
              <option value="">Select</option>
              <option value="800x600">800x600</option>
              <option value="1024x768">1024x768</option>
              <option value="1280x800">1280x800</option>
              <option value="1920x1080">1920x1080</option>
              <option value="3840x2160 (4K)">3840x2160 (4K)</option>
              <option value="Other">Other</option>
            </select>
          </label>
          <label>Quantity: 
            <input type="number" name="projector-qty-?" min="0" placeholder="e.g. 1" required>
          </label>
        </fieldset>

        <fieldset id="powerstrip-template">
          <!-- <legend>Powerstrip</legend> -->
          <label>Make: 
            <input type="text" name="powerstrip-make-?" placeholder="e.g. Belkin" required>
          </label>
          <label>Model: 
            <input type="text" name="powerstrip-model-?" placeholder="e.g. F9H620-06" required>
          </label>
          <label>Color: 
            <input type="text" name="powerstrip-color-?" placeholder="e.g. White" required>
          </label>
          <label>Number of Plugs: 
            <input type="number" name="powerstrip-outlets-?" min="0" placeholder="e.g. 6" required>
          </label>
        </fieldset>

        <fieldset id="extension-template">
          <!-- <legend>Extension Cord</legend> -->
          <label>Color: 
            <input type="text" name="extension-color-?" placeholder="e.g. Orange" required>
          </label>
          <label>Length (ft): 
            <input type="number" name="extension-length-?" min="0" placeholder="e.g. 25" required>
          </label>
        </fieldset>

        <fieldset id="mic-template">
          <!-- <legend>Microphone/Recorder</legend> -->
          <label>Type: 
            <input type="text" name="mic-type-?" placeholder="e.g. Handheld, Tabletop" required>
          </label>
          <label>Brand: 
            <input type="text" name="mic-brand-?" placeholder="e.g. Shure" required>
          </label>
          <label>Model: 
            <input type="text" name="mic-model-?" placeholder="e.g. SM58" required>
          </label>
          <label>Quantity: 
            <input type="number" name="mic-qty-?" min="0" placeholder="e.g. 1" required>
          </label>
        </fieldset>

        <fieldset id="other-template">
          <!-- <legend>Other</legend> -->
          <label>Description:
            <textarea name="other-desc-?" placeholder="Describe other equipment" rows="3"></textarea>
          </label>
          <label>Quantity: 
            <input type="number" name="other-qty-?" min="0" placeholder="e.g. 1" required>
          </label>
        </fieldset>        
      </div>
    </main>
    <?php include '../footer.php' ?>
  </body>
  
</html>