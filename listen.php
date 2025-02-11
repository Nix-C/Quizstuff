<?php 
  $OK = TRUE;
  $payload = file_get_contents('php://input') ?? null;
  if($payload){
    require_once 'config.php';
    $json_paylod = json_decode($payload);
    $secret = QM_HOOK_SECRET;
    $github_signature =  $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
    $expected_signature = 'sha256=' . hash_hmac('sha256', $payload, $secret);
  
    if (hash_equals($expected_signature, $github_signature)){
      if(
        $_SERVER['HTTP_X_GITHUB_EVENT'] === "release" 
        && $json_paylod->action === "released"
      ){

        # Download from github using the api endpoint
        $token = GH_API_TOKEN;
        $url = $json_paylod->release->zipball_url;
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
          $errorMessage = date('Y-m-d H:i:s') . " - cURL Error: " . curl_error($ch) . "\n";
          file_put_contents("logs.txt", $errorMessage, FILE_APPEND);
        } else {
          $tagName = $json_paylod->release->tag_name;

          // Update qm-version.json
          $fileLocation = "/downloads/" . $tagName . ".zip";
          

          file_put_contents("qm-version.json", '
{
  "version": "'.$tagName.'",
  "asset_url": "'.$fileLocation.'"
}');
          // Put file
          file_put_contents("./assets/downloads/" . $tagName . ".zip", $response);

        }
        
        curl_close($ch);
      }
      
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