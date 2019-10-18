/**************************************************************************/
/*!
    @file     CalibrateSniffingTrinket.ino
    @author   G.Krocker (Mad Frog Labs)
    @license  GNU GPLv3
 
    Arduino (TrinketPro) sketch for SniffingTrinket
    
    This sketch is for calibration purposes only. To calibrate the Gas sensor
    put the SniffingTrinket into outside air, preferably at 20°C, 35% RH. Let
    the sketch run until the RZero value is stable (at least 20min). Note the
    measured RZero value and put it in the MQ135.h library. The gas sensor 
    should now be calibrated.
 
    @section  HISTORY
 
    v1.0 - First release
*/
/**************************************************************************/
#include <ESP8266WiFi.h>
#include <WiFiUdp.h>
#include <ESP8266HTTPClient.h>

#include "DHT.h"
#include "MQ135.h"


const char* ssid = "";
const char* password = "";

const char* dataHost = "192.168.1.1";
const int   dataPort = 80;



// Define on which pins the rest of the circuit is connected
#define DHTPIN D6     // what pin we're connected to
#define ANALOGPIN A0


// Define timeout in ms
#define TIMEOUT 3000

// Alarmlevels
#define TEMPALARM 30
#define HUMALARM 65
#define CO2ALARM 1000



// Define the type of sensor used, needs to be changed if different DHT sensor
// is in use - see adafruit DHT library example
#define DHTTYPE DHT22   // DHT 11 

// Initialize DHT sensor for normal 16mhz Arduino
DHT dht(DHTPIN, DHTTYPE);

// Initialize the gas Sensor
MQ135 gasSensor = MQ135(ANALOGPIN);

float humidity;
float temperature;
int   hour;

/**************************************************************************/
/*!
@brief  Setup function

Make sure everything is set up correctly
*/
/**************************************************************************/
void setup() {
  
  //Set up the serial terminal
  Serial.begin(9600); 

  // start the humidity/temp sensor
  dht.begin();
  
  // inital connect
  WiFi.mode(WIFI_STA);
  WiFiStart();

}

///////////////////
// (re-)start WiFi
///////////////////
void WiFiStart()
{
  // Connect to WiFi network
  Serial.println();
  Serial.println();
  Serial.print("Connecting to ");
  Serial.println(ssid);
  
  WiFi.begin(ssid, password);
  
  while (WiFi.status() != WL_CONNECTED) 
  {
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.println("WiFi connected");
  
  // Print the IP address
  Serial.println(WiFi.localIP());
  Serial.println(WiFi.macAddress());
}

/**************************************************************************/
/*!
@brief  The main loop

*/
/**************************************************************************/
void loop() {
  // Wait a few seconds between measurements.
  // TODO: This is not ideal - better put the Arduino to sleep here
  delay(TIMEOUT);

  // Reading temperature or humidity takes about 250 milliseconds!
  // Sensor readings may also be up to 2 seconds 'old' (its a very slow sensor)
  float h = dht.readHumidity();
  // Read temperature as Celsius
  float t = dht.readTemperature();
  // Read temperature as Fahrenheit - in case you still don't use the SI
  //float f = dht.readTemperature(true);

  // Check if any reads failed and exit early (to try again).
  if (isnan(h) || isnan(t)) {
    Serial.println("Failed to read from DHT sensor!");
    return;
  }


  // Read out the Gas Sensor
  float rzero = gasSensor.getRZero();
  // Do not use temperature/humidity correction, it is broken!!!
  //float ppm = gasSensor.getCorrectedPPM(t, h);
  // TODO: Implement some sanity check here!
  
  // Print the measurements to the serial port
  Serial.print("T: "); 
  Serial.print(t);
  Serial.print(" *C\t");
  Serial.print("H: "); 
  Serial.print(h);
  Serial.print(" %\t");
  Serial.print("RZero: ");
  Serial.print(rzero);
  Serial.println(" kOhm");


  WiFiClient client;
  HTTPClient http;

  String url = "/includes/setCalibrationData.php?mac=";
    url += WiFi.macAddress();
    url += "&temp=";
    url += t;
    url += "&hum=";
    url += h;
    url += "&rzero=";
    url += rzero;

  Serial.print("Connecting to");
  Serial.print(dataHost);
  Serial.println(url);
  
  http.begin(dataHost,dataPort,url);
  int httpCode = http.GET();

  if (httpCode) {
    if (httpCode == 200) {
      Serial.println("Data Sent!");

      String payload = http.getString();
      hour = payload.toInt();
      Serial.println(payload);
    }else{
      Serial.println("Something went wrong!");
    }
  }
  http.end();
  

}
