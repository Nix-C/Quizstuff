<?php
  $page_title = "Quizstuff";
?>
<!DOCTYPE html>
<html lang="en">
  <?php include 'head.php'; ?>

  <body>
    <div id="canvas">
      <div id="radial-1"></div>
      <div id="radial-2"></div>
    </div>
    <?php include 'header.php'; ?>

    <main>
      <section class="container">
        <h1 class="container-header">Need Help?</h1>
        <p>Send us a message!</p>
        <form id="help-form">
          <label for="name">Your Name:</label><br>
          <input type="text" id="name" name="name" required><br><br>

          <label for="email">Your Email:</label><br>
          <input type="email" id="email" name="email" required><br><br>
          
          <label for="message">Message:</label><br>
          <textarea id="message" name="message" rows="5" required></textarea><br><br>

          <button value="Send Email">Send Message</button>
        </form>
      </section>
    </main>

    <?php include 'footer.php'; ?>
    <script src="help.js"></script>
  </body>
</html>