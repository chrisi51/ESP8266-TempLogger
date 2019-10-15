# ESP8266-TempLogger
Little software for using an ESP8266 for logging temperature and humidty (and possibly air quality) on your flat/house.

## Pinout
![alt text](https://raw.githubusercontent.com/chrisi51/ESP8266-TempLogger/master/pinout.jpg "Pinout")


## Temperatur and Humidity measurement
The ESP8266 will connect to your WIFI and afterwards send the new measured data to your database. After all is done, it falls into a deepsleep and get awake after the given sleep time.

## OTA - Update
The ESP8266 will check for a new firmware everytime, it sends the sensors data. If you have uploaded a new binary and set the new version for a specific ESP8266 (according to its MAC-Adress) it will download and install the new firmware and reboot after that.

If you have installed a buzzer, it will beep 2 times to inform you about the successfull update.
