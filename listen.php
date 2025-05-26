<?php
  require_once 'config.php';
  $OK = TRUE;
  $payload = file_get_contents('php://input') ?? null; // Accept input from webhook

  // Download asset and return sha1 string.
  function downloadAsset($asset){
    // Download from github using the api endpoint
    $token = GH_API_TOKEN;
    
    $headers = [
      "X-GitHub-Api-Version: 2022-11-28",
      "Authorization: Bearer ${token}",
      "Accept: application/octet-stream", // Tells GitHub to send the raw binary
      "User-Agent: MyGitHubClient" // GitHub API requires a User-Agent header
    ];

    $ch = curl_init($asset->url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
      $OK = FALSE;
      $errorMessage = date('Y-m-d H:i:s') . " - cURL Error: " . curl_error($ch) . "\n";
      echo $errorMessage . "\n";
      file_put_contents("logs.txt", "ERROR: " . $errorMessage, FILE_APPEND);
      curl_close($ch);
    } else {

      $httpCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
      if($httpCode !== 200) { // Check GH token!
        $OK = FALSE;
        $errorMessage = date('Y-m-d H:i:s') . " - Download Error: Discord responded with status code " . $httpCode . "\n";
        echo $errorMessage . "\n";
        file_put_contents("logs.txt", "ERROR: " . $errorMessage, FILE_APPEND);
        curl_close($ch);
      } else {
        $assetPath = "/installers/" . $asset->name;
        // Put file
        file_put_contents("." . $assetPath, $response); 

        // Get sha1 sum
        $sha1Value = hash_file('sha1', "." . $assetPath);

        curl_close($ch);
        return [
          'assetPath' => $assetPath,
          'sha1' => $sha1Value
        ];
      }


    }
  }

  // Helper function
  function findMatchingArch(array $array, $arch ) {
    echo "Looking for a matching " . $arch . "\n";
    foreach($array as $index => $item) {
      print_r( $item->architecture . " and " . $arch . "\n");
      if($item->architecture === $arch){
        return $index;
      }
    }
    return false;
  }

  if($payload){
    $json_paylod = json_decode($payload);
    // Verify signature
    $secret = GH_HOOK_RELEASE_SECRET;
    $github_signature =  $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
    $expected_signature = 'sha256=' . hash_hmac('sha256', $payload, $secret);

    // React to "released"
    if (
      $_SERVER['HTTP_X_GITHUB_EVENT'] === "release" 
      && $json_paylod->action === "released"
    ) {
      // If signature good, do stuff
      if(hash_equals($expected_signature, $github_signature)){
        file_put_contents("logs.txt", date('Y-m-d H:i:s') . " - Webhook: Release Received!" . "\n", FILE_APPEND);
        
        $versionDataFile = @file_get_contents("qm-version.json");
        $currentVersionData = false;
        if($versionDataFile) {
          $currentVersionData = json_decode( $versionDataFile );
        }

        // Set installerData equal to current installer data
        $installerData = $currentVersionData->installers ?? [];

        // Loop through assets available
        $assets = $json_paylod->release->assets;
        
        foreach ($assets as $asset) {
          if(str_ends_with($asset->name, ".exe") || str_ends_with($asset->name, ".deb")) {
            // Download asset & get returned asset path + sha1sum
            ['assetPath' => $assetPath, 'sha1' => $sha1Value] = downloadAsset($asset);
            
            if($assetPath != null) {
              /**
               * We offer 4 versions:
               * - Windows 10, 11
               * - Windows 7, 8
               * - Linux Intel/AMD Ubuntu
               * - Linux Raspberry Pi
               */

              if(
                str_ends_with($asset->name, ".exe")
              ){ // Windows 10, 11
                // Parse filename
                $parts = explode('-', pathinfo($asset->name, PATHINFO_FILENAME));
                list(, $ver, $installVer, $arch) = $parts;

                $os = "windows";
                $version = $ver . "-" . $installVer;
              } else if (
                str_ends_with($asset->name, ".deb")
              ) { // Linux AMD/Intel Ubuntu
                // Parse filename
                $parts = explode('_', pathinfo($asset->name, PATHINFO_FILENAME));
                list(, $versionString) = $parts;

                $parts = explode('-', $versionString);
                list($ver, $installVer, $arch) = $parts;

                $os = "linux";
                $version = $ver . "-" . $installVer;
              } else {
                $arch = "unknown";
                $version = "unkown";
                $os = "unknown";
                $OK = FALSE;
                echo "ERROR: Unknown installer: " . $asset->name . "\n";
              }
              
              // Check to see if installer setting exists, if so update it.
              $matchingArch = findMatchingArch($installerData, $arch);

              if($matchingArch){
                file_put_contents("logs.txt", date('Y-m-d H:i:s') . " - Webhook: Found a match!" . "\n", FILE_APPEND);
                $installerData[$matchingArch] = (object) [
                  'architecture' => $arch,
                  'version' => $version,
                  'os' => $os,
                  'sha1sum' => $sha1Value,
                  'url' => $assetPath
                ];

              } else {
                // Push to installers
                array_push($installerData, (object) [
                  'architecture' => $arch ?? "unknown",
                  'version' => $version ?? "unknown",
                  'os' => $os ?? "unknown",
                  'sha1sum' => $sha1Value,
                  'url' => $assetPath
                ]);
              }




            } else {
              $OK = FALSE;
              echo "ERROR: There was an issue with downloading the asset." . "\n";
            }

          }
          // Put json data

          
        }
        if($OK) {
          file_put_contents("qm-version.json", '{ "installers": ' . json_encode($installerData) . '}');
        }
      } else {
        $OK = FALSE;
        echo "ERROR: Incorrect hash." . "\n";
      }
      
    // } else if (false){
    //   # Do auto deployment here later
    } else {
      $OK = FALSE;
      echo "ERROR: Unhandled or unrecognized event." . "\n";
    }
  } else {
    $OK = FALSE;
    echo "Unable to read webhook payload." . "\n";
  }

  if($OK){
    http_response_code(200); // OK
  } else {
    http_response_code(404); // Bad request
  }
?>