<?php
  $page_title = strcmp($page_title, "Quizstuff") ? $page_title . " | Quizstuff" : "Quizstuff";
?>

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link
    rel="shortcut icon"
    href="/assets/images/ostritch.ico"
    type="image/x-icon"
  />
  <link rel="stylesheet" href="/style.css" />
  <script src="/script.js" defer></script>
  <script
    src="https://challenges.cloudflare.com/turnstile/v0/api.js"
    async
    defer
  ></script>
  <title><?php echo $page_title ?></title>
</head>