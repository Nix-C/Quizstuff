<?php
  $page_title = "Quizstuff";
?>
<!DOCTYPE html>
<html lang="en">
  <?php include 'head.php'; ?>

  <body>

    <?php include 'header.php'; ?>

    <main>
      <section class="container">
        <h1 class="container-header">Contact Us</h1>
        <p style="max-width: 600px; text-align: center;">Need help with placing an order?<br>Have questions about equipment?<br>Let us help!</p>
        <form id="help-form">
          <label for="name">Your Name:</label><br>
          <input type="text" id="name" name="name" required><br><br>

          <label for="email">Your Email:</label><br>
          <input type="email" id="email" name="email" required><br><br>
          
          <label for="message">Message:</label><br>
          <textarea id="message" name="message" rows="5" required></textarea><br><br>

          <button id="submit" value="Send Email">Send Message</button>
        </form>
        <p id="submit-message"></p>
      </section>
    </main>

    <?php include 'footer.php'; ?>
    <script src="help.js"></script>
  </body>
</html>