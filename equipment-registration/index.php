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
  <body>
    <?php include '../header.php'; ?>
    <main>
      <section class="container">
        <h1>Equipment Registration</h1>
        <form>
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
              <input type="text" name="name-first" id="name-first" required>
            </label>
            <label for="name-last">Last Name
              <input type="text" name="name-last" id="name-last" required>
            </label>
            <label for="district">District & Field
              <input type="text" name="district" id="district" required>
            </label>
            <label for="email">Email
              <input type="email" name="email" id="email" required>
            </label>
            <label for="phone">Phone
              <input type="tel" name="phone" id="phone" required>
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
                  <input type="text" maxlenth="30" name="brand" required>
              </label>
              <label>Operating System
                <select name="operating-system">
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
                <select name="port-type">
                  <option>Please Select</option>
                  <option value="None">None</option>
                  <option value="Built-in">Built-in</option>
                  <option value="PCMCIA">PCMCIA</option>
                  <option value="USB Adapter">USB Adapter</option>
                </select>
              </label>
              <label>QuizMachine Version
                <input type="text" name="quizmachine-version" maxlenth="15">
              </label>
              <label>Username
                <input type="text" name="username" maxlenth="30">
              </label>
              <label>Password
                <input type="password" name="password" maxlenth="30">
              </label>
            </fieldset>
          </div>

          </fieldset>
        </form>
      </section>
    </main>
    <?php include '../footer.php' ?>
  </body>
  
</html>