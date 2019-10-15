<?php

  require_once("../config.php");
  require_once("db_access.php");

  $date_from = $_GET["date_from"];
  $date_to = $_GET["date_to"];
  
  if(!($stmt = $mysqli->prepare("SELECT * FROM (SELECT id, (UNIX_TIMESTAMP(datum)*1000) as datum, temp, humidity FROM ". $db_table_data ." WHERE datum BETWEEN CAST('" . $date_from . "' AS DATE) AND CAST('" . $date_to . "' AS DATE) ORDER BY id DESC LIMIT 400000) d ORDER BY d.id ASC"))){
     echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;    
  }
  $stmt->execute();
  $res = $stmt->get_result();

  $temp = array();
  $humidity = array();
  while ($row = $res->fetch_assoc()){
    array_push($temp,array(($row["datum"]),$row["temp"]));
    array_push($humidity,array(($row["datum"]),$row["humidity"]));
  }
  $data = array("temp"=>$temp,"humidity"=>$humidity);
  //echo str_replace('"','',json_encode($data));
  echo json_encode($data,JSON_NUMERIC_CHECK);

?>