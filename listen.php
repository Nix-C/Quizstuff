<?php
  require_once 'config.php';
  $OK = TRUE;
  $payload = file_get_contents('php://input') ?? null;

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
        // Loop through assets available
        $assets = $json_paylod->release->assets;
        $installerData = [];
        foreach ($assets as $asset) {
          if(str_ends_with($asset->name, ".exe") || str_ends_with($asset->name, ".deb")) {
            // Download asset
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
                && preg_match('/-([^-.]+)\.exe/i', $asset->name, $matches)
              ){ // Windows 10, 11
                $os = "windows";
                $arch = $matches[1] ? $matches[1] : "unrecognized"; # TODO: UPDATE THIS!
              } else if (
                str_ends_with($asset->name, ".deb")
                && preg_match('/-([^-.]+)\.deb/i', $asset->name, $matches)
              ) { // Linux AMD/Intel Ubuntu
                $os = "linux";
                $arch = $matches[1] ? $matches[1] : "unrecognized";
              } else {
                $arch = "unknown";
                $os = "unknown";
                $OK = FALSE;
                echo "ERROR: Unknown installer: " . $asset->name . "\n";
              }
              
              // Push to installers
              array_push($installerData, (object) [
                'architecture' => $arch,
                'os' => $os,
                'sha1sum' => $sha1Value,
                'url' => $assetPath
              ]);


            } else {
              $OK = FALSE;
              echo "ERROR: There was an issue with downloading the asset." . "\n";
            }

          }
          // Put json data
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