<?php
  $page_title = "FAQs";
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
        <h1 class="container-header">OK, how does this all work?</h1>

        <p class="container-text">
          First, a laptop or desktop computer (we do have Dell laptops with for
          a reasonable price) is needed. We do recommend the USB Interface Box.
          Considering how most newer computers do not have a parallel port, a
          PCMCIA or Express (34mm or 44mm) port is needed for the parallel
          Interface Box. See the notes below for specific computer requirements.
          <br /><br />
          USB to Parallel converter cables (such as from Radio Shack) will not
          work so do not waste your time. Neither will serial to parallel
          cables.
          <br /><br />
          The pads (see links for ordering the correct pads or new ones from
          QuizStuff) connect to the interface box which the connects to the
          computer's USB or parallel port. QuizMachine reads the data from the
          pads to determine which quizzer was the first to jump.
          <br /><br />
          QuizMachine not only determines which quizzer was first to jump, but
          also keeps the score by quizzer and team. A printed score sheet is
          also available. Once a round is complete a file can be created and
          loaded into QMServer and statistics for all rooms, whether by a USB
          key or some networking scheme, is compiled within a few minutes.
          QMServer produces reports for both individuals and teams. These
          reports can be exported to a spreadsheet file and used in other
          programs is desired. The points for teams can be calculated
          automatically using Olympic, Modified Olympic or can be Win/Loss. The
          amount of points for the Olympic styles can be configured (ie, 5
          points for 1st, 3 for second and 1 for third). The type used can also
          be selected, Olympic, Modified Olympic or W/L and can be different
          from one tournament to another.
          <br /><br />
          The <u>free 30 day trial</u> version may also be used for training and
          practice with QMServer. See the download section for the User Manual.
        </p>

        <img id="HIW" src="/assets/images/HIW.gif" alt="Hardware diagram." />

        <a
          href="http://www.quizstuff.com/zencart/index.php?main_page=login&zenid=502e276d8dbff6ccf0d336295bd5ffdb"
          class="button"
          >Order Equippment</a
        >
      </section>

      <section class="container">
        <h1 class="container-header">USB Interface Box And Pads</h1>

        <p class="container-text">
          ***Please note that if you have Windows 7,8 or 10 running on a 64bit
          computer, you must first connect to the Internet before connecting the
          USB Interface box. <u>A special driver may be required</u>.
          <br /><br />
          The USB Interface Box requires a USB driver to be loaded. This happens
          automatically when you start your computer and then connect the USB
          Interface box. Please be patient as it may take a minute or more for
          the driver to load. Once loaded, start QuizMachine. Go to setup,
          hardware tab. Change the setting from Lpt1 Interface to QuizMachine
          USB Qbox. QuizMachine should display a window that indicates the USB
          Box is Disconnected. Then almost immediately, another window should be
          displayed indicating that the USB Box is now Connected. No external
          electrical power should be required.
        </p>

        <a
          href="http://www.quizstuff.com/zencart/index.php?main_page=login&zenid=502e276d8dbff6ccf0d336295bd5ffdb"
          class="button"
          >Order Equippment</a
        >
      </section>

      <section class="container">
        <h1 class="container-header">Some Common Issues</h1>

        <p class="container-text">
          <b><u>Wireless Networking:</u></b> Please note that wired or wireless
          networking will dramatically affect performance on computers running
          any Windows 98 operating system and files cannot be exported. It is
          not recommended to use any processor less than 1.8Ghz Core Duo or
          Windows XP SP3 Pro with to network with QMServer. <br /><br /><br />
          <b><u>The program does not recognize any pads:</u></b> First, you can
          order new pads that are sewn on all 4 sides from us for $70 per
          string. See the store for more information. These pads work with any
          version of QuizMachine <br /><br />
          To use the USB Interface Box on Windows XP just connect the box and
          start QuizMachine. The drivers will be loaded automatically. For
          Windows 7, 8 or 10 make sure the computer is connected to the Internet
          before attempting to use the USB Interface Box the first time. It
          needs to obtain a special driver from a web site that is not obvious.
          Not following these instructions will cause the Interface Box to fail.
          <br /><br />
          In most cases a correction in the system setup can be accomplished by
          starting in the control panel.
          <br /><br />
          1. &nbsp;&nbsp; Click Start
          <br />
          2. &nbsp;&nbsp; Click Settings then Control Panel (or just Control
          Panel depending on your OS)
          <br />
          3. &nbsp;&nbsp; Click System
          <br />
          4. &nbsp;&nbsp; Click Hardware
          <br />
          5. &nbsp;&nbsp; Click Device Manager
          <br />
          6. &nbsp;&nbsp; Expand the Ports (COM and LPT) selection
          <br />
          7. &nbsp;&nbsp; There should be a port listed such as COM1 when the
          box is connected
          <br />
          8. &nbsp;&nbsp; To start over, remove that device
          <br />
          9. &nbsp;&nbsp; Start the install process again (see notes above)
          <br /><br /><br />
          For more support please contact us at <u>quizstuff@quizstuff.com</u>.
          <br /><br /><br />
        </p>
      </section>
    </main>

    <?php include 'footer.php'; ?>
  </body>
</html>
