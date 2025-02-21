<?php 
  $OK = TRUE;
  $payload = file_get_contents('php://input') ?? null;
  if($payload){
    require_once 'config.php';
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
        // Find first .exe available in assets
        $assets = $json_paylod->release->assets;
        $targetAsset = null;
        foreach ($assets as $asset) {
          if(str_ends_with($asset->name, ".exe")){
            $targetAsset = $asset;
            break;
          }
        }
        if(!$targetAsset){
          $OK = FALSE;
          $error = "No assets in release.";
          file_put_contents("logs.txt", "ERROR: " . $error, FILE_APPEND);
        }

        // Download from github using the api endpoint
        $token = GH_API_TOKEN;
        $url = $targetAsset->url;
        $headers = [
          "X-GitHub-Api-Version: 2022-11-28",
          "Authorization: ${token}",
          "User-Agent: MyGitHubClient" // GitHub API requires a User-Agent header
        ];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
          $OK = FALSE;
          $errorMessage = date('Y-m-d H:i:s') . " - cURL Error: " . curl_error($ch) . "\n";
          file_put_contents("logs.txt", "ERROR: " . $errorMessage, FILE_APPEND);
        } else {
          // Update qm-version.json
          $tagName = $json_paylod->release->tag_name;
          $fileLocation = "/assets/downloads/" . $targetAsset->name;
        
          file_put_contents("qm-version.json", '{"version": "'.$tagName.'", "asset_url": "'.$fileLocation.'"}');
          
          // Put file
          file_put_contents("." . $fileLocation, $response);
        }
        
        curl_close($ch);
      }
      
    // } else if (false){
    //   # Do auto deployment here later
    } else {
      $OK = FALSE;
    }
  } else {
    $OK = FALSE;
  }

  if($OK){
    http_response_code(200); // OK
  } else {
    http_response_code(404); // Bad request
  }
?>