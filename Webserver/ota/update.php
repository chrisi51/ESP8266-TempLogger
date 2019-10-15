<?PHP
require_once("../config.php");
require_once("../includes/db_access.php");

header('Content-type: text/plain; charset=utf8', true);

function check_header($name, $value = false) {
//error_log($name.": ".$_SERVER[$name],0);

    if(!isset($_SERVER[$name])) {
        return false;
    }
    if($value && $_SERVER[$name] != $value) {
        return false;
    }
    return true;
}

function sendFile($path) {
    header($_SERVER["SERVER_PROTOCOL"].' 200 OK', true, 200);
    header('Content-Type: application/octet-stream', true);
    header('Content-Disposition: attachment; filename='.basename($path));
    header('Content-Length: '.filesize($path), true);
    header('x-MD5: '.md5_file($path), true);
    readfile($path);
}

if(!check_header('HTTP_USER_AGENT', 'ESP8266-http-Update')) {
    header($_SERVER["SERVER_PROTOCOL"].' 403 Forbidden', true, 403);
    echo "only for ESP8266 updater!\n";
    exit();
}

if(
    !check_header('HTTP_X_ESP8266_STA_MAC') ||
    !check_header('HTTP_X_ESP8266_AP_MAC') ||
    !check_header('HTTP_X_ESP8266_FREE_SPACE') ||
    !check_header('HTTP_X_ESP8266_SKETCH_SIZE') ||
    !check_header('HTTP_X_ESP8266_CHIP_SIZE') ||
    !check_header('HTTP_X_ESP8266_SDK_VERSION') ||
    !check_header('HTTP_X_ESP8266_VERSION')
) {
    header($_SERVER["SERVER_PROTOCOL"].' 403 Forbidden', true, 403);
    echo "only for ESP8266 updater! (header)\n";
    exit();
}


$select_firmware = "SELECT firmware FROM ". $db_table_sensors." WHERE mac='" . $_SERVER['HTTP_X_ESP8266_STA_MAC'] . "'";
$result = mysqli_query($mysqli, $select_firmware ) or die("Fehler beim Eintragen der Daten in die Datenbank!");
$row = $result->fetch_assoc();

if(isset($row["firmware"]) && $row["firmware"] != "" && $row["firmware"] != "0") {
    if($row["firmware"] != $_SERVER['HTTP_X_ESP8266_VERSION']) {
        sendFile("./files/ESP-DHT22-OTA.ino.nodemcu-".$row["firmware"].".bin");
    } else {
        header($_SERVER["SERVER_PROTOCOL"].' 304 Not Modified', true, 304);
    }
    exit();
}

header($_SERVER["SERVER_PROTOCOL"].' 500 no version for ESP MAC', true, 500);
