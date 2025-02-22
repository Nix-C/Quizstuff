<?php
  $page_title = "Downloads";
  $assetData = json_decode(file_get_contents("qm-version.json") ?? '{"installers": []}');
  // Get installer data.
  $x64InstallerData = array_filter($assetData->installers, function($installerData) {
    return isset($installerData->architecture) && $installerData->architecture === "x64";
  });
  $x64InstallerData = reset($x64InstallerData);
  
  $x86InstallerData = array_filter($assetData->installers, function($installerData) {
    return isset($installerData->architecture) && $installerData->architecture === "x86";
  });
  $x86InstallerData = reset($x86InstallerData);
  
  $amd64InstallerData = array_filter($assetData->installers, function($installerData) {
    return isset($installerData->architecture) && $installerData->architecture === "amd64";
  });
  $amd64InstallerData = reset($amd64InstallerData);

  $arm64InstallerData = array_filter($assetData->installers, function($installerData) {
    return isset($installerData->architecture) && $installerData->architecture === "arm64";
  });
  $arm64InstallerData = reset($arm64InstallerData);

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
        <h1 class="container-header">Purchase QuizMachine</h1>
        <p class="container-text">
          To unlock all features of QuizMachine, you'll need to purchase user key. This includes a lifetime of free upgrades!
        </p>
        <p>Not ready to buy? Download a version of QuizMachine to try with limited functionality.</p>
        <div class="downloads-container">
          <img class="download-card--icon--small" src="/assets/images/windows.png" alt="Microsoft Windows icon." />
          <p>or</p>
          <img class="download-card--icon--small" src="/assets/images/ubuntu-logo.svg" alt="Ubuntu icon." />
          <p>or</p>
          <img class="download-card--icon--small" src="/assets/images/raspberry-pi.svg" alt="Raspberry Pi icon." />
        </div>

        <a
          href="https://quizstuff.com/store/order-form.php"
          class="button"
        >
        Purchase Key 
        </a>
        
      </section>

      <section class="container">
        <h1 class="container-header">Download For Windows</h1>
        <?php if ($x64InstallerData || $x86InstallerData) : ?>
          <div class="downloads-container">
            <?php if ($x64InstallerData) : ?>
              <div class="download-card">
                <h4>Windows 10, 11 (x64)</h4>
                <img class="download-card--icon" src="/assets/images/windows.png" alt="Microsoft Windows icon." />
                <a href="<?= $x64InstallerData->url ?>" class="button">Download &nbsp; <?= $x64InstallerData->version ?></a>
                <small>
                  <a class="download-card--link" href="/assets/downloads/QuizMachine Documentation v.6.0.0.pdf">
                    <img class="download-card--icon--inline" src="/assets/images/pdf-file.png" alt="PDF icon." />
                    Download PDF Manual
                  </a>
                </small>
              </div>
            <?php endif ?>
            <?php if ($x86InstallerData) : ?>
              <div class="download-card">
                <h4>Windows 7, 8 (x86)</h4>
                <img class="download-card--icon" src="/assets/images/windows.png" alt="Microsoft Windows icon." />
                <a href="<?= $x86InstallerData->url ?>" class="button">Download &nbsp; <?= $x86InstallerData->version ?></a>
                <small>
                  <a class="download-card--link" href="assets\downloads\QuizMachine Documentation v.5.0.0.pdf">
                    <img class="download-card--icon--inline" src="/assets/images/pdf-file.png" alt="PDF icon." />
                    Download PDF Manual
                  </a>
                </small>
              </div>
            <?php endif ?>
          </div>

        <?php else : ?>
            <h4>Currently unavailable.</h4>
          <?php endif ?>
      </section>

      
        <section class="container">
          <h1 class="container-header">Download For Linux</h1>
          <?php if ($amd64InstallerData || $arm64InstallerData) : ?>
            <div class="downloads-container">
              <?php if ($amd64InstallerData) : ?>
                <div class="download-card">
                  <h4>Ubuntu 24.04 LTS+ (amd64)</h4>
                  <img src="/assets/images/ubuntu-logo.svg" alt="Ubuntu icon." />
                  <a href="<?= $amd64InstallerData->url ?>" class="button">Download &nbsp; <?= $amd64InstallerData->version ?></a>
                </div>
              <?php endif ?>
              <?php if ($arm64InstallerData) : ?>
                <div class="download-card">
                  <h4>Raspberry Pi (arm64)</h4>
                  <img class="download-card--icon" src="/assets/images/raspberry-pi.svg" alt="Raspberry Pi icon." />
                  <a href="<?= $arm64InstallerData->url ?>" class="button">Download &nbsp; <?= $arm64InstallerData->version ?></a>
                </div>
              <?php endif ?>
            </div>
            <p><small>⚠️ Version 6 Linux manual coming soon. ⚠️</small></p> 
          <?php else : ?>
            <h4>Currently unavailable.</h4>
          <?php endif ?>
        </section>
      
<!-- 
      <section class="container">
        <h1 class="container-header">Download a Free User Manual</h1>

        <p class="container-text">
          QuizMachine User Manual (PDF) Version 4.0.0 includes instructions for
          QMServer; contact us at <u>quizstuff@quizstuff.com</u> for more
          information about using QMServer.
        </p>

        <img src="/assets/images/pdf-file.png" alt="PDF icon." />
        <a href="assets/downloads/Quizmachine Users Guide v.4.0.0.pdf" class="button">Download the User Manual</a>
      </section> -->
    </main>

    <?php include 'footer.php'; ?>
  </body>
</html>
