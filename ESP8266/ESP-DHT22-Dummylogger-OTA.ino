/*---------------------------------------------------
HTTP 1.1 Temperature & Humidity Webserver for ESP8266 
for ESP8266 adapted Arduino IDE

by Stefan Thesen 05/2015 - free for anyone

Connect DHT21 / AMS2301 at GPIO2
---------------------------------------------------*/

#include <ESP8266WiFi.h>
#include <WiFiUdp.h>
//#include "time_ntp.h"
#include "DHT.h"
#include <ESP8266HTTPClient.h>
#include <ESP8266httpUpdate.h>

const char* ssid = "";
const char* password = "";

const char* dataHost = "192.168.1.1";
const int   dataPort = 80;

const char* updateHost = "192.168.1.1";
const int   updatePort = 80;
const char* version = "1010";

//////////////////////////////
// DHT22 / AMS2302 is at GPIO2
//////////////////////////////
#define DHTTYPE DHT22   // DHT 22  (AM2302)

#define DEBUG true  // true = Ausgabe im Seriellen Monitor
#define aTp 17.271  // fuer Formel Taupunkt
#define bTp 237.7   // fuer Formel Taupunkt
float taupunkt;     // Berechneter Taupunkt
float taupunktTmp;  // Berechneter Taupunkt 
float humidity;
float temperature;
float tempindex;
int   hour;
//#define sleeptime 300e6 // 5 Minuten Sleep
//#define sleeptime 30e6  // 30 Sekunden Sleep
#define sleeptime 15e6  // 15 Sekunden Sleep

// init DHT; 3rd parameter = 16 works for ESP8266@80MHz
//DHT dht(D6, DHTTYPE, 16); 
DHT dht(D6, DHTTYPE); 


#define BUZZER_PIN  D2

/////////////////////
// the setup routine
/////////////////////
void setup() 
{
  pinMode(BUZZER_PIN, OUTPUT);
  digitalWrite(BUZZER_PIN, LOW);
  // setup globals
  // start serial
  Serial.begin(9600);
  
  Serial.print("WLAN Temperatur und Feuchtigkeitslogger v");
  Serial.println(version);

  dht.begin();  
  getData();

  // inital connect
  WiFi.mode(WIFI_STA);
  WiFiStart();

  sendData();

  if(humidity>60){buzzerNotify();}
  else{delay(600);}

  autoUpdate();

  Serial.println("DeepSleep");
  ESP.deepSleep(sleeptime);
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

void getData()
{
  byte retry = 3;
  do{
    humidity = dht.readHumidity();
    temperature = dht.readTemperature();
    retry-- ;
  } 
  while( ((isnan(humidity))||(isnan(temperature)) && (retry > 0)) );
 
//Compute heat index (gefühlte Temperatur)
  float tempindex = dht.computeHeatIndex(temperature, humidity,false);

//Berechnung Taupunkt
//SollH = (Hmax + (dht1_t - Tmin) * dH);  // Ideale Wohlfühlfeuchte berechnen
  taupunktTmp = ((aTp * temperature) / (bTp + temperature)) + log(humidity / 100);
  taupunkt = (bTp * taupunktTmp) / (aTp - taupunktTmp);

  if(DEBUG) {
    Serial.println("");
    Serial.print(F("Temperatur: "));
    Serial.print(temperature);
    Serial.println("");
    Serial.print(F("Feuchtigkeit: "));
    Serial.print(humidity);
    Serial.println("");
    Serial.print(F("Temperatur Index: "));
    Serial.print(tempindex);
    Serial.println("");
    Serial.print(F("Taupunkt: "));
    Serial.println(taupunkt);
  }
}

void sendData()
{ 
  WiFiClient client;
  HTTPClient http;

  String url = "/includes/setData.php?mac=";
    url += WiFi.macAddress();
    url += "&temp=";
    url += temperature;
    url += "&hum=";
    url += humidity;
    url += "&tempindex=";
    url += tempindex;
    url += "&tau=";
    url += taupunkt;

  Serial.print("Connecting to");
  Serial.print(dataHost);
  Serial.println(url);
  
  http.begin(dataHost,dataPort,url);
  int httpCode = http.GET();

  if (httpCode) {
    if (httpCode == 200) {
      String payload = http.getString();
      hour = payload.toInt();
      Serial.println(payload);
    }else{
      Serial.println("Something went wrong!");
    }
  }
  http.end();
}


void autoUpdate()
{
  ESPhttpUpdate.rebootOnUpdate(false);
  t_httpUpdate_return ret = ESPhttpUpdate.update(updateHost, updatePort, "/ota/update.php", version);
  switch(ret) {
    case HTTP_UPDATE_FAILED:
        Serial.println("[update] Update failed.");
        break;
    case HTTP_UPDATE_NO_UPDATES:
        Serial.println("[update] Update no Update.");
        break;
    case HTTP_UPDATE_OK:
        Serial.println("[update] Update ok.");
        buzzerNotify();
        ESP.restart();
        break;
  }  
}
void buzzerNotify()
{
  if (!shouldSoundBeMuted()){
    digitalWrite(BUZZER_PIN, HIGH); 
    Serial.println(" Turn On BUZZER! ");
    delay(200);
    digitalWrite(BUZZER_PIN, LOW); 
    Serial.println(" Turn Off BUZZER! ");
    delay(200);
    digitalWrite(BUZZER_PIN, HIGH); 
    Serial.println(" Turn On BUZZER! ");
    delay(200);
    digitalWrite(BUZZER_PIN, LOW); 
    Serial.println(" Turn Off BUZZER! ");
  }else{
    delay(600);
  }
}
void buzzerAlert()
{
  if (!shouldSoundBeMuted()){
    digitalWrite(BUZZER_PIN, HIGH); 
    Serial.println(" Turn On BUZZER! ");
    delay(200);
    digitalWrite(BUZZER_PIN, LOW); 
    Serial.println(" Turn Off BUZZER! ");
    delay(200);
    digitalWrite(BUZZER_PIN, HIGH); 
    Serial.println(" Turn On BUZZER! ");
    delay(200);
    digitalWrite(BUZZER_PIN, LOW); 
    Serial.println(" Turn Off BUZZER! ");
    delay(200);
    digitalWrite(BUZZER_PIN, HIGH); 
    Serial.println(" Turn On BUZZER! ");
    delay(200);
    digitalWrite(BUZZER_PIN, LOW); 
    Serial.println(" Turn Off BUZZER! ");
    delay(200);
    digitalWrite(BUZZER_PIN, HIGH); 
    Serial.println(" Turn On BUZZER! ");
    delay(1000);
    digitalWrite(BUZZER_PIN, LOW); 
    Serial.println(" Turn Off BUZZER! ");
    delay(200);
  }
}

boolean shouldSoundBeMuted()
{
  if (hour > 8 && hour < 22) return false;
  else return true;
}

void loop(){

}
