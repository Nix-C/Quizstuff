<?php
  #NOTE: We are limited to 60 calls per OAuth2 and Shipping Options each

  $env = parse_ini_file('.env'); # Access .env variables
  $QS_ORIGIN_ZIP = $env['QS_ORIGIN_ZIP'];
  $destinationZip = 60134;  
  $weight = 35; # Weight in Ibs
  $json = json_encode([
    "pricingOptions" => [
      [
        "priceType" => "RETAIL"
      ]
    ],
    "originZIPCode" => $QS_ORIGIN_ZIP,
    "destinationZIPCode" => "60134",
    "packageDescription" => [
      "weight" => (float) $weight, // Ensuring numeric values are correct
      "length" => 6,
      "height" => 12,
      "width" => 4,
      "girth" => 1,
      "mailClass" => "PARCEL_SELECT"
    ],
    "shippingFilter" => "PRICE"
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);


  # OAuth2 clientCredentials, shipments
  $KEY = $env['USPS_KEY'];
  $SECRET = $env['USPS_SECRET'];
  $json_get_token = json_encode([
    "grant_type" => "client_credentials",
    "client_id" => $KEY,
    "client_secret" => $SECRET,
    "scope" => "shipments"
  ]);
  $curl_token = curl_init("https://apis.usps.com/oauth2/v3/token");
  curl_setopt($curl_token, CURLOPT_POST, 1);
  curl_setopt($curl_token, CURLOPT_POSTFIELDS, $json_get_token);
  curl_setopt($curl_token, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl_token, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Accept: application/json"
  ]);
  $result_token = curl_exec($curl_token);
  curl_close($curl_token);

  $result_token_data = json_decode($result_token);
  $token = $result_token_data->access_token;
  
  # Build POST cURL request
  # NOTE: api-cat.usps.com is the TEST env. Switch to api.usps.com in production
  $curl = curl_init("https://api-cat.usps.com/shipments/v3/options/search");
  curl_setopt($curl, CURLOPT_POST, 1);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
  curl_setopt($curl, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Accept: application/json",
    "Authorization: Bearer $token" // Correct way to pass OAuth2 token
  ]);
  $result = curl_exec($curl);
  curl_close($curl);


  echo var_dump(var_dump(curl_getinfo($curl)));
?>