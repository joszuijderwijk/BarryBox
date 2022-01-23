# BarryBox
BarryBox is an MQTT controlled Text-to-Speech speaker. It is connected to a frontend from which you can directly send messages and sounds (from a soundboard) to the speaker. My personal BarryBox is hosted at: https://barrybox.hetberenhuis.nl.

<img src="https://i.imgur.com/nki11Zw.jpg" alt="BarryBox" width="600"/>

The BarryBox has the following features:
  * You can setup WiFi credentials and a username on the first startup;
  * Receive messages (audio stream / TTS message) over MQTT;
  * Play a sound at startup;
  * Sending its status (online / offline);
  * Queueing audio streams that it has to play;
  * Stop the currently playing stream if you press the button;
  * Clear the queue of streams if you press the button long.

The backend adds the following features:
  * Make HTTP endpoints available for sending messages and obtaining the status;
  * Save incoming messages into a database;
  * Restricting access with an API key;
  * Automatically register newly connected BarryBoxes by username.

With the frontend you can easily access any BarryBox over the web.

## Hardware

The BarryBox is based on an ESP32 microcontroller, which controls a PCM5102 DAC.

<table>
  <tr>
    <td valign="top"><img src="https://i.imgur.com/YZvjGPT.jpg"/></td>
    <td valign="top"><img src="https://i.imgur.com/uDoUA4R.jpg"/></td>
  </tr>
</table>

## Quickstart
### Flashing the ESP32
1. Install the [ESP32 Arduino core](https://github.com/espressif/arduino-esp32) from the Arduino Boards Manager.
2. Install the required libraries from the Library Manager:
    - [ESP32-audioI2S](https://github.com/schreibfaul1/ESP32-audioI2S)
    - [WiFiManager-ESP32] (https://github.com/tzapu/WiFiManager)[^1]
    - [esp8266-google-tts](https://github.com/horihiro/esp8266-google-tts)
    - [Arduino Client for MQTT](https://github.com/knolleary/pubsubclient)
    - [ArduinoJson](https://github.com/bblanchon/ArduinoJson)
    - [ArduinoQueue](https://github.com/EinarArnason/ArduinoQueue)
3. Rename `config.sample.h` to `config.h` and add your MQTT server and credentials to the file.
4. Open `BarryBox.ino` using the Arduino software.
5. Select your ESP32 board from the `Tools > Boards` menu.
6. Flash your board by selecting `Sketch > Upload`.

[^1]: Use the development version through the `Add .ZIP Library` feature. Do not use the latest release (V0.16) as it is not compatible with the ESP32.

### Setting up the Node-RED backend
TODO

### Setting up the frontend (Docker/Podman)
The included `docker-compose.yml` file is compatible with Docker and (rootless) Podman.
1. Rename `include/config.sample.php` to `include/config.php`, add your API key and API URL and set the default user.
2. Start the container using `docker-compose up -d`.
