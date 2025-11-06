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
              <select name="event"  >
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
              <input type="text" name="name_first" id="name-first"  >
            </label>
            <label for="name-last">Last Name
              <input type="text" name="name_last" id="name-last"  >
            </label>
            <label for="district">District & Field
              <input type="text" name="district" id="district"  >
            </label>
            <label for="email">Email
              <input type="email" name="email" id="email"  >
            </label>
            <label for="phone">Phone
              <input type="tel" name="phone" id="phone"  >
            </label>
          </fieldset>
          <!-- Equipment -->
          <h2>Equipment</h2>
          <div>
            <h3>Laptops</h3>
            <!-- Laptop block -->
            <fieldset id="laptop-1">
              <legend>Laptop <span id="laptop-i">1</span></legend>
              <label>Brand
                  <input type="text" maxlenth="30" name="brand"  >
              </label>
              <label>Operating System
                <select name="laptop-operating_system-1">
                  <option>Please Select</option>
                  <option value="Win 11+">Windows 11 (or newer)</option>
                  <option value="Win 10">Windows 10</option>
                  <option value="Win 7/8">Windows 7/8</option>
                  <option value="Older Windows">Older Windows</option>
                  <option value="Linux">Linux</option>
                  <option value="Other">Other</option>
                </select>
              </label>
              <label>Port Type
                <select name="laptop-port_type-1">
                  <option>Please Select</option>
                  <option value="None">None</option>
                  <option value="Built-in">Built-in</option>
                  <option value="PCMCIA">PCMCIA</option>
                  <option value="USB Adapter">USB Adapter</option>
                </select>
              </label>
              <label>QuizMachine Version
                <input type="text" name="laptop-quizmachine_version-1" maxlenth="15">
              </label>
              <label>Username
                <input type="text" name="username" maxlenth="30">
              </label>
              <label>Password
                <input type="password" name="password" maxlenth="30">
              </label>
            </fieldset>
          </div>
          <div>
            <fieldset>
              <legend>Pads <span>1</span></legend>
              <label>Color
                <select name="pads-color-1">
                  <option>Please Select</option>
                  <option value="Red">Red</option>
                  <option value="Blue">Blue</option>
                  <option value="Green">Green</option>
                  <option value="Yellow">Yellow</option>
                  <option value="Other">Other</option>
                </select>
              </label>
              <label>Quantity
                <input type="number" name="pads-quantity-1" >
              </label>
            </fieldset>
          </div>
          <div>
          <fieldset>
            <legend>Interface Box</legend>
            <label>Type
              <select name="interface-type-1">
                <option>Please Select</option>
                <option value="USB">USB</option>
                <option value="Parallel">Parallel</option>
              </select>
            </label>
            <label>Quantity <input type="number" name="interface-qty-1" min="0"></label>
          </fieldset>
          </div>

          <div>
            <fieldset>
              <legend>Monitor</legend>
              <label>Brand 
                <input type="text" name="monitor-brand-1" placeholder="e.g. Samsung">
              </label>
              <label>Size (inches):
                <select name="monitor-size-1">
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
                <select name="monitor-resolution-1">
                  <option value="">Please Select</option>
                  <option value="HD (1080)">HD (1080)"</option>
                  <option value="UHD (3840)">UHD (3840)"</option>
                  <option value="Other">Other"</option>
                </select>
              </label>
              <label>Connection Type
                <select name="monitor-connection_type-1">
                  <option value="">Please Select</option>
                  <option value="HD (1080)">HD (1080)"</option>
                  <option value="UHD (3840)">UHD (3840)"</option>
                  <option value="Other">Other"</option>
                </select>                
              </label>
            </fieldset>
          </div>
          <div>
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
                  <option value="3840x2160 (4K)">3840x2160 (4K)</option>
                  <option value="Other">Other</option>
                </select>
              </label>
              <label>Quantity: <input type="number" name="projector_qty" min="0" placeholder="e.g. 1"></label>
            </fieldset>
          </div>
          <div>
            <fieldset>
              <legend>Powerstrip</legend>
              <label>Make: <input type="text" name="powerstrip_make" placeholder="e.g. Belkin"></label>
              <label>Model: <input type="text" name="powerstrip_model" placeholder="e.g. F9H620-06"></label>
              <label>Color: <input type="text" name="powerstrip_color" placeholder="e.g. White"></label>
              <label>Number of Plugs: <input type="number" name="powerstrip_outlets" min="0" placeholder="e.g. 6"></label>
            </fieldset>
          </div>
          <fieldset>
            <legend>Extension Cord</legend>
            <label>Color: <input type="text" name="extension_color" placeholder="e.g. Orange"></label>
            <label>Length (ft): <input type="number" name="extension_length" min="0" placeholder="e.g. 25"></label>
          </fieldset>
          <!-- <fieldset>
            <legend>Microphone/Recorder</legend>
            <label>Type: <input type="text" name="mic-type" placeholder="e.g. Handheld, Tabletop"></label>
            <label>Brand: <input type="text" name="mic-brand" placeholder="e.g. Shure"></label>
            <label>Model: <input type="text" name="mic-model" placeholder="e.g. SM58"></label>
            <label>Quantity: <input type="number" name="mic_qty" min="0" placeholder="e.g. 1"></label>
          </fieldset> -->
          <fieldset>
            <legend>Other</legend>
            <label>Description:
              <textarea name="other_desc" placeholder="Describe other equipment" rows="3"></textarea>
            </label>
            <label>Quantity: <input type="number" name="other_qty" min="0" placeholder="e.g. 1"></label>
          </fieldset>
          <div id="agreement" style="display: flex; gap: 10px; margin-top: 20px;">
            <input type="checkbox" name="agree" id="agree"  >
            <p>I confirm that the above information is accurate and I am authorized to register this equipment.</p>
          </div>
          <button type="submit" id="button--submit" class="button" name="submit">Register Equipment</button>
        </form>
      </section>
    </main>
    <?php include '../footer.php' ?>
  </body>
  
</html>