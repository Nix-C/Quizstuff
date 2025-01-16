<?php
  $env = parse_ini_file('.env');
  $QS_ORIGIN = $env['QS_ORIGIN'];

  echo $QS_ORIGIN;
?>