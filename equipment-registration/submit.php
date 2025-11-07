<?php
  require_once './config/database.php';
  require_once '../config.php';
  $data = json_decode(file_get_contents('php://input')); 

  // Step 1 - Validate turnstile token
  $cf_url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

  $cf_data = [
      'secret' => CF_TURNSTILE_SECRET,
      'response' => $data->token
  ];

  $cf_options = [
    'http' => [
      'header' => "Content-type: application/x-www-form-urlencoded\r\n",
      'method' => 'POST',
      'content' => http_build_query($cf_data)
    ]
  ];

  $cf_context = stream_context_create($cf_options);
  $cf_response = json_decode(file_get_contents($cf_url, false, $cf_context));
  // if ($cf_response->success == false) {
  //   $OK = FALSE;
  //   echo "Invalid token.";
  //   http_response_code(403);
  //   exit();
  // }
  echo var_dump($cf_response);