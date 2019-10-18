# ESP8266-TempLogger
Little software for using an ESP8266 for logging temperature and humidty (and possibly air quality) on your flat/house.

## What you need:
* ESP8266 (https://www.amazon.de/gp/product/B0754LZ73Z)
* DHT22 https://amzn.to/2nWRlo0
* Buzzer (https://amzn.to/2BnAUUT)
* MQ135 (https://amzn.to/35Iqhdr)
* Hole-Driller (https://amzn.to/2OXTavZ)
* Box (https://amzn.to/2MX6ZYY)
* Wires & Boards (https://amzn.to/31vKVtM)


## Setup
* install apache with php and mysql
* install mysql
* import Database/structure.sql to your database
* upload the content of Webserver to your Webserver
* modify /config.php to connect to database
* flash ESP8266/ESP-DHT22-Dummylogger-OTA.ino to your ESP8266
* insert your ESP8266 with its MAC-Adress to the table sensors
* watch your log with /sensor.php or /all.php

## Pinout
![alt text](https://raw.githubusercontent.com/chrisi51/ESP8266-TempLogger/master/pinout.jpg "Pinout")

## Dashboard
![alt text](https://raw.githubusercontent.com/chrisi51/ESP8266-TempLogger/master/dashboard.jpg "Dashboard")

## Temperatur and Humidity measurement
The ESP8266 will connect to your WIFI and afterwards send the new measured data to your database. After all is done, it falls into a deepsleep and get awake after the given sleep time.

## OTA - Update
The ESP8266 will check for a new firmware everytime, it sends the sensors data. If you have uploaded a new binary and set the new version for a specific ESP8266 (according to its MAC-Adress) it will download and install the new firmware and reboot after that.

If you have installed a buzzer, it will beep 2 times to inform you about the successfull update.
