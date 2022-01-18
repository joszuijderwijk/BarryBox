// TODO: Shouldn't make use of String

#include <Arduino.h>

enum StreamableType {SOUND, STREAM, SAY, NIL};

// Object defining soundboard sounds / streams / TTS
class Streamable {

  StreamableType type;
  String data; // text / sound / stream
  String language;

  public:
  StreamableType Type(){ return type;}
  String Data(){return data;}
  String Language(){return language;}

  void SetType(StreamableType type){this->type = type;}
  void SetData(String data){this->data = data;}
  void SetData(const char* data){this->data = String(data);}
  void SetLanguage(String language){this->language = language;}
  void SetLanguage(const char* language){this->language = String(language);}

  Streamable(){
    type = StreamableType::NIL;
  }

  Streamable(StreamableType _type, String _data, String _language = ""){
    type = _type;
    data = _data;
    language = _language;
  }

  Streamable(StreamableType _type, const char* _data, const char* _language=""){
    type = _type;
    data = String(_data);
    language = String(_language);
  }
};
