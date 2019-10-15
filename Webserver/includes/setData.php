<?php
require_once("../config.php");
require_once("db_access.php");

isset($_GET['mac']) ? $mac=$_GET['mac'] : $mac='';
isset($_GET['temp']) ? $temp=$_GET['temp'] : $temp='';
isset($_GET['hum']) ? $hum=$_GET['hum'] : $hum='';
isset($_GET['tempindex']) ? $tempindex=$_GET['tempindex'] : $tempindex='';
isset($_GET['tau']) ? $tau=$_GET['tau'] : $tau='';


$stmt = $mysqli->prepare("INSERT INTO ". $db_table_data ." (mac, temp, humidity, tempindex, taupunkt) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sdddd", $mac, $temp, $hum, $tempindex, $tau);
$stmt->execute();
$stmt->close();

// return current hour of time for handling the buzzer mute function
echo date("G");
?>
