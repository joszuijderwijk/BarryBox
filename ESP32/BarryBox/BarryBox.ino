#include <FS.h>
#include "SPIFFS.h"

#include "WiFi.h"
#include "Audio.h"

#include <DNSServer.h> // Local DNS Server used for redirecting all requests to the configuration portal
#include <WebServer.h> // Local WebServer used to serve the configuration portal
#include <WiFiManager.h>

#include <google-tts.h>
#include <PubSubClient.h> // MQTT library
#include <SPIFFS.h>
#include <ArduinoJson.h>
#include <ArduinoQueue.h>

#include "config.h"
#include "streamable.cpp"

// Pins
#define I2S_DOUT   25
#define I2S_BCLK   27
#define I2S_LRC    26
#define PIN_BUTTON 23

// Maximum length of client name/username
#define CLIENT_NAME_LEN 40

Audio audio;

WiFiClient wifiClient;           // WiFi
PubSubClient client(wifiClient); // MQTT
bool isConnected = false;

char client_name[CLIENT_NAME_LEN];

// Button settings
unsigned long buttonTimer;
const int DEBOUNCE = 250;
const int LONG_BUTTON_PRESS = 2000;
bool buttonPressed = false;
bool buttonPressedLong = false;

//flag for saving data
bool shouldSaveConfig = false;

//callback notifying us of the need to save config
void saveConfigCallback()
{
  Serial.println("Should save config");
  shouldSaveConfig = true;
}

// MQTT channels
#define PREFIX "barrybox/"
String sub_channel;
String conn_channel;
String all_channel;
String soundboard_channel;
String stream_channel;
String tts_channel;

// Welcome
StreamableType W_TYPE = StreamableType::SOUND;
char *W_TEXT = "gamecube";

const char *DEFAULT_LANGUAGE = "nl";

char *languages[] = {"af", "sq", "ar", "hy", "az", "eu", "be", "bg", "ca", "zh-cn", "zh-tw", "hr", "cs", "da", "nl",
					 "en", "et", "tl", "fi", "fr", "gl", "ka", "de", "el", "ht", "iw", "hi", "hu", "is", "id", "ga",
					 "it", "ja", "ko", "lv", "lt", "mk", "ms", "mt", "no", "fa", "pl", "pt", "ro", "ru", "sr", "sk",
					 "sl", "es", "sw", "sv", "th", "tr", "uk", "ur", "vi", "cy", "yi"};

// Queue capable of holding max 20 items
ArduinoQueue<Streamable *> Q(20);

/*
 MQTT commands
  barrybox/%c/say         : TextToSpeech (JSON)
  barrybox/%c/stream      : Play http stream
    msg == "stop"         : Stop currently playing stream
  barrybox/%c/soundboard  : Play from soundboard
*/

// initialize variables
void initialize()
{
  String prefix = PREFIX + String(client_name);

  // not the most elegant way
  sub_channel = prefix + "/#";
  conn_channel = prefix + "/connection";
  all_channel = String(PREFIX) + "/all";
  soundboard_channel = prefix + "/soundboard";
  stream_channel = prefix + "/stream";
  tts_channel = prefix + "/say";
}

// Start up WifiManager
void setWifiManager()
{

  Serial.println("mounting FS...");

  if (SPIFFS.begin())
  {
    Serial.println("mounted file system");
    if (SPIFFS.exists("/config.json"))
    {
      //file exists, reading and loading
      Serial.println("reading config file");
      File configFile = SPIFFS.open("/config.json", "r");
      if (configFile)
      {
        Serial.println("opened config file");
        size_t size = configFile.size();
        // Allocate a buffer to store contents of the file.
        std::unique_ptr<char[]> buf(new char[size]);

        configFile.readBytes(buf.get(), size);

        DynamicJsonDocument json(1024);
        auto deserializeError = deserializeJson(json, buf.get());
        serializeJson(json, Serial);
        if (!deserializeError)
        {
          Serial.println("\nparsed json");
          strlcpy(client_name, json["client_name"], CLIENT_NAME_LEN);
        }
        else
        {
          Serial.println("failed to load json config");
        }
        configFile.close();
      }
    }
  }
  else
  {
    Serial.println("failed to mount FS");
  }

  WiFiManagerParameter username("client_name", "Username", client_name, CLIENT_NAME_LEN);
  WiFiManagerParameter footer("<p>&copy; 2022 by <a href=\"https://joszuijderwijk.nl\">Jos Zuijderwijk</a></p>");

  WiFiManager wifiManager;
  wifiManager.setSaveConfigCallback(saveConfigCallback);
  wifiManager.addParameter(&username);
  wifiManager.addParameter(&footer);

  // client_name can't be "all" or empty
  if (strcmp(client_name, "") == 0 || strcmp(client_name, "all") == 0)
  {
    Serial.println("Client name invalid.");
    wifiManager.resetSettings();
  }

  if (wifiManager.autoConnect("BarryBox"))
  {
    isConnected = true;
  }

  //read updated parameters
  strlcpy(client_name, username.getValue(), CLIENT_NAME_LEN);

  Serial.println("The values in the file are: ");
  Serial.println("\tUsername : " + String(client_name));

  //save the custom parameters to FS
  if (shouldSaveConfig)
  {
    Serial.println("saving config");
    DynamicJsonDocument json(1024);

    json["client_name"] = client_name;

    File configFile = SPIFFS.open("/config.json", "w");
    if (!configFile)
    {
      Serial.println("failed to open config file for writing");
    }

    serializeJson(json, Serial);
    serializeJson(json, configFile);

    configFile.close();
  }
}

void setup()
{

  Serial.begin(115200);

  setWifiManager();

  initialize();

  pinMode(PIN_BUTTON, INPUT_PULLUP);

  // MQTT
  client.setServer(MQTT_SERVER, MQTT_PORT);
  client.setCallback(callback);

  // Audio
  audio.setPinout(I2S_BCLK, I2S_LRC, I2S_DOUT);
  audio.setVolume(21); // 0...21

  // Play welcome sound
  Streamable *welcome = new Streamable(W_TYPE, W_TEXT);
  play(welcome);
  delete welcome;
}

// Try restore the MQTT connection
void reconnect()
{
  // Loop until we're reconnected
  while (!client.connected())
  {
    if (client.connect(client_name, MQTT_USER, MQTT_PASS, conn_channel.c_str(), 0, 1, "0"))
    {
      // Send Hello World!
      client.publish(conn_channel.c_str(), "1", 1);
      client.subscribe(sub_channel.c_str());
    }
  }
}

// Check if language is a valid Google Translate code
bool isValidLanguage(const char *language)
{

  for (const char *lang : languages)
  {
    if (strcmp(lang, language) == 0)
      return true;
  }
  return false;
}

// Handle incoming messages
void callback(char *topic, byte *payload, unsigned int len)
{

  String msg = ""; // payload
  for (int i = 0; i < len; i++)
  {
    msg += ((char)payload[i]);
  }

  Streamable *s = new Streamable();

  if (strcmp(topic, tts_channel.c_str()) == 0)
  {

    // Got TTS message
    s->SetType(SAY);

    StaticJsonDocument<256> doc;
    deserializeJson(doc, payload);

    if (!doc.isNull())
    {
      const char *text = doc["text"];
      const char *lang = doc["language"];

      s->SetData(text);

      if (isValidLanguage(lang))
        s->SetLanguage(lang);
      else
        s->SetLanguage(DEFAULT_LANGUAGE);
    }
    else
    {
      s->SetData(msg);
    }
  }
  else if (strcmp(topic, stream_channel.c_str()) == 0)
  {
    if (msg == "stop")
      audio.stopSong();
    else
    {
      s->SetType(STREAM);
      s->SetData(msg);
    }
  }
  else if (strcmp(topic, soundboard_channel.c_str()) == 0)
  {
    s->SetType(SOUND);
    s->SetData(msg);
  }

  if (!Q.isFull())
    Q.enqueue(s);
}

// Start playing
void play(Streamable *s)
{
  if (s->Data() != "")
  {

    switch (s->Type())
    {
    case SOUND:
    {
      String host = String(SOUNDBOARD) + s->Data() + ".mp3";
      audio.connecttohost(host.c_str());
    }
    break;
    case STREAM:
      audio.connecttohost(s->Data().c_str());
      break;
    case SAY:
      audio.connecttospeech(s->Data().c_str(), s->Language().c_str());
      break;
    }
  }
}

// Stop current audio
void PressButton()
{
  Serial.println("Button has been pressed.");
  audio.stopSong();
}

// Stop audio and empty queue
void PressButtonLong()
{
  Serial.println("Button has been pressed long.");
  while (!Q.isEmpty())
    Q.dequeue();

  audio.stopSong();
}

void loop()
{
  if (!client.connected() && isConnected)
  {
    reconnect();
  }

  if (!digitalRead(PIN_BUTTON))
  {
    if (!buttonPressed && millis() - buttonTimer > DEBOUNCE)
    {
      buttonPressed = true;
      buttonTimer = millis();
    }
    if (millis() - buttonTimer > LONG_BUTTON_PRESS && !buttonPressedLong)
    {
      buttonPressedLong = true;
      PressButtonLong();
    }
  }
  else if (buttonPressed)
  {
    if (buttonPressedLong)
      buttonPressedLong = false;
    else
      PressButton();

    buttonPressed = false;
  }

  // Play the next one in the queue
  if (!audio.isRunning() && !Q.isEmpty())
  {
    Streamable *s = Q.dequeue();
    play(s);
    delete s;
  }

  client.loop();
  audio.loop();
}

void audio_info(const char *info)
{
  Serial.print("info        ");
  Serial.println(info);
}

void audio_eof_speech(const char *info)
{
  Serial.print("eof_speech  ");
  Serial.println(info);
}
