<?php

  require_once("../config.php");
  require_once("db_access.php");

  if(isset($_GET["modulname"])){

    if(strpos($_GET["modulname"],"Diff-")!==false){
	$modulnamen = str_replace("Diff-","",$_GET["modulname"]);
	$sensors=explode("-",$modulnamen );

  	$date_from = $_GET["date_from"];
	$date_to = $_GET["date_to"];
  
	$temp = array();
	$humidity = array();

	foreach($sensors as $value)
	{
	  $temp[$value] = array();
	  $humidity[$value] = array();

	  if(!($stmt = $mysqli->prepare("SELECT mac FROM ". $db_table_sensors ." WHERE name='".$value."'"))){
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;    
	  }
	  $stmt->execute();
	  $res = $stmt->get_result();
	  $row = $res->fetch_assoc();
	  $mac = $row["mac"];


  	  if(!($stmt2 = $mysqli->prepare("SELECT * FROM (SELECT id, (UNIX_TIMESTAMP(datum)*1000) as datum, temp, humidity FROM ". $db_table_data ." WHERE mac='" . $mac . "' AND datum BETWEEN CAST('" . $date_from . "' AS DATE) AND CAST('" . $date_to . "' AS DATE) ORDER BY id DESC LIMIT 400000) d ORDER BY d.id ASC"))){
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;    
	  }
  	  $stmt2->execute();
	  $res2 = $stmt2->get_result();

	  while ($row = $res2->fetch_assoc()){
	    array_push($temp[$value],array(($row["datum"]),$row["temp"]));
	    array_push($humidity[$value],array(($row["datum"]),$row["humidity"]));
	  }
	}
	$temp["diff"]=$temp[$sensors[0]];
	$humidity["diff"]=$humidity[$sensors[0]];
	foreach($temp[$sensors[0]] as $key => $value)
	{
		$temp["diff"][$key][1] = $temp[$sensors[0]][$key][1] - $temp[$sensors[1]][$key][1];
		$humidity["diff"][$key][1] = $humidity[$sensors[0]][$key][1] - $humidity[$sensors[1]][$key][1];
	}


	$data = array("temp"=>$temp["diff"],"humidity"=>$humidity["diff"]);
	echo json_encode($data,JSON_NUMERIC_CHECK);
	exit();

    }else{

      if(!($stmt = $mysqli->prepare("SELECT mac FROM ". $db_table_sensors ." WHERE name='".$_GET["modulname"]."'"))){
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;    
      }
      $stmt->execute();
      $res = $stmt->get_result();
      $row = $res->fetch_assoc();
      $_GET["modul"] = $row["mac"];
   }
}

  $date_from = $_GET["date_from"];
  $date_to = $_GET["date_to"];
  
  if(!($stmt = $mysqli->prepare("SELECT * FROM (SELECT id, (UNIX_TIMESTAMP(datum)*1000) as datum, temp, humidity FROM ". $db_table_data ." WHERE mac='" . $_GET["modul"] . "' AND datum BETWEEN CAST('" . $date_from . "' AS DATE) AND CAST('" . $date_to . "' AS DATE) ORDER BY id DESC LIMIT 400000) d ORDER BY d.id ASC"))){
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