<?php

  $mysqli = new mysqli($db_host, $db_user, $db_pw, $db_name);
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }
?>