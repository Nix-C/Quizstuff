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
        echo "Retrieving latest release files";
        # Download from github using the Get a Release api endpoint
        # https://docs.github.com/en/rest/releases/releases?apiVersion=2022-11-28#get-a-release
  
      }
      
    } else {
      echo $json->hook->config->secret;
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