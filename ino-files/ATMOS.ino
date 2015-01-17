////////////////////////////////////////////////
//                                            //
// ATMOS                                      //
//                                            //
////////////////////////////////////////////////

#include <SPI.h>
#include <Ethernet.h>
#include <Barometer.h>
#include <Wire.h>

// структра описывающие наш параметр
struct key_param_t{
    char *key;
    char *value;
};

#define MAX_PARAMS 20 // сколько параметров максимально мы умеем парсить
key_param_t params[MAX_PARAMS]; // в этот массис будем сохранять наши парсенные параметры
byte parsedParams = 0; // сколько параметров нам удалось напарсить

// настройки пинов
#define pin_GHUM A0  // Влажность почвы
#define pin_LDR A1   // LDR-датчик
#define pin_REL1 8   // Реле №1: свет
#define pin_REL2 6   // Реле №2: вода
#define pin_REL3 5   // Реле №3: тепло
#define pin_REL4 2   // Реле №4: < ... >
#define SD_PIN 4     // SD-карта
#define pin_KEY 3    // Силовой ключ (отопление, свет)

// настройки Ethernet
byte mac[]                    = { 0x90, 0xA2, 0xDA, 0x0D, 0xA8, 0x77 }; // MAC-адрес
byte ip[]                     = { 192, 168, 210, 57 };                  // Статический IP-адрес
byte subnet[]                 = { 255, 255, 255, 0 };                   // Маска подсети
byte gateway[]                = { 192, 168, 210, 1 };                   // Адрес шлюза
byte mydns[]                  = { 192, 168, 210, 1 };                   // Адрес DNS-сервера (необязателен)
byte serv[]                   = { 5, 17, 192, 164 };                    // Адрес сервера (Google)
unsigned int reconnectTimeout = 180000;                                 // Тайм-аут переподключения

int LIGHTNESS_MIN         = 50;    // настройки LDR
int TEMPERATURE_MIN       = 20;    // настройки Barometer
const int HUMIDY_MIN      = 200;   // настройки для Влажности почвы
const int HUMIDY_MAX      = 700;
unsigned int timeWait     = 60000; // интервал полива
unsigned int timeWatering = 5000;  // длительность полива

int LIGHTNESS;
int TEMPERATURE;
long PRESSURE;
int ALTITUDE;
unsigned int G_HUMIDY;

bool automode = false; // флаг автоматического режима

char okHeader[] =
  "HTTP/1.0 200 OK\r\n"
  "Content-Type: text/json\r\n"
  "Pragma: no-cache\r\n"
  "Access-Control-Allow-Origin: *\r\n"
;
char denyHeader[] =
  "HTTP/1.0 401 Unauthorized\r\n"
  "Content-Type: text/html\r\n"
  "Access-Control-Allow-Origin: *\r\n"
  "\r\n"
  "<h1>401 Unauthorized</h1>\r\n"
;
char jsonResponse[80];

EthernetServer server = EthernetServer(80); // Создаём Ethernet-сервер, ожидающий соединения на 80 порт
EthernetClient client;                      // Создаём клиента, для соединения с указанным IP-адресом
Barometer myBarometer;                      // Барометр

/**
 * Инициализация Ethernet-соединения
 */
bool initEthernet() {
  // инициируем соединение (DHCP)
  if (Ethernet.begin(mac)) {
    Serial.println(Ethernet.localIP());

    server.begin(); // ожидаем соединения клиентов на сервер (80 порт)

    return true;
  } else {
    Serial.print("FAIL: automode - write to Archive and wait ");
    Serial.print((reconnectTimeout / 1000));
    Serial.println(" seconds");

    automode = true;

    return false;
  }
}

/*
 * Ответ на опрос устройств, высылаем свой локальный IP в сети
 */
void answer4serching() {
  union IPAddressConverter {
    uint32_t ipInteger;
    uint8_t ipArray[4];
  };

  IPAddressConverter ipAddress;
  ipAddress.ipInteger = Ethernet.localIP();
  char searchResp[] = "{\"device_ip\": \"%d.%d.%d.%d\"}";

  Serial.println("searching");
  sprintf(jsonResponse, searchResp, ipAddress.ipArray[0], ipAddress.ipArray[1], ipAddress.ipArray[2], ipAddress.ipArray[3]);
}

/**
 * Установка новых значений конфурации
 */
void setConfigOptions() { Serial.println("setting"); }

/**
 * Спрашиваем освещённость среды
 */
int askLDR() {
  // считываем уровень освещённости (0 - свет, 1023 - темнота)
  return (1023 - analogRead(pin_LDR)); // обратнопропорционально
}

/**
 * Спрашиваем температуру воздуха
 */
int askTemperature() { return (int)myBarometer.bmp085GetTemperature(myBarometer.bmp085ReadUT()); }

/**
 * Спрашиваем атмосферное давление
 */
long askPressure() { return (long)myBarometer.bmp085GetPressure(myBarometer.bmp085ReadUP()); }

/**
 * Спрашиваем высоту
 */
int askAltitude() { return (int)myBarometer.calcAltitude(PRESSURE); }

/**
 * Спрашиваем датчик влажности почвы
 */
unsigned int askHumidy() { return analogRead(pin_GHUM); }

/**
 * Проверка сухости почвы: если сухо, то поливаем
 */
void watering() {
  static unsigned long wait     = millis() + timeWatering;
  static unsigned int oldhumidy = 0;
  unsigned long def             = millis();

  if (G_HUMIDY != oldhumidy) { oldhumidy = G_HUMIDY; }

  if (G_HUMIDY > HUMIDY_MIN || (wait - def) < (timeWait - timeWatering)) {
    digitalWrite(pin_REL2, LOW); // Прекращаем полив
  }

  if (wait != 0 && (wait - def) > 50) { return; } // если время ещё не вышло
  else                                { wait = 0; }

  // если влажность почвы меньше минимума
  if (G_HUMIDY < HUMIDY_MIN) {
    digitalWrite(pin_REL2, HIGH); // Поливаем

    Serial.print("Waiting ");
    Serial.print(timeWait / 1000);
    Serial.println(" seconds");

    wait = millis() + 1 * timeWait;
    delay(1000);
  }
}

/**
 * Проверка освещённости среды: если темно, то включаем свет
 */
void lighting() {
  bool tooDark = (LIGHTNESS < LIGHTNESS_MIN);
  
  digitalWrite(pin_REL1, tooDark);
}

/**
 * Проверка температуры среды: если холодно, то включаем обогреватель
 */
void warming() {
  bool tooCold = (TEMPERATURE < TEMPERATURE_MIN);

  digitalWrite(pin_REL3, tooCold);
}

/**
 * Опрос датчиков, обновление данных
 */
static void getSensorData() {
  Serial.println("asking");

  LIGHTNESS    = askLDR();
  TEMPERATURE  = askTemperature();
  PRESSURE     = askPressure();
  ALTITUDE     = askAltitude();
  G_HUMIDY     = askHumidy();

  char str[] =
  "{"
    "\"lightness\": %d,"
    "\"temperature\": %d,"
    "\"pressure\": %ld,"
    "\"altitude\": %d,"
    "\"g_humidy\": %d"
  "}";

  sprintf(
    jsonResponse,
    str,
    LIGHTNESS,
    TEMPERATURE,
    PRESSURE,
    ALTITUDE,
    G_HUMIDY);
}

/**
 * Запись данных с датчиков на SD-карту
 */
void write2SD() {}

/**
 * Чтение данных с SD-карты
 */
void readFromSD() {}

/**
 * Автоматический контроль данных и управление манипуляторами
 */
void autoWork() {
  watering(); // поливаем
  lighting(); // освещаем
  warming(); // греем
}

void monitoring() {
  // при подсоединении клиента появляются байты доступные для чтения:
  client = server.available(); // если есть в клиенте непрочитанные байты, то возвращаем в client объект, его описывающий

  if (client.available()) {
    char queryFromServer[client.available()];
    int i = 0;

    jsonResponse[0] = '\0';
    
    // пока есть непрочитанные байты
    while (client.available()) {
      queryFromServer[i] = client.read();

      if (queryFromServer[i] == '\n') { queryFromServer[i + 1] = '\0'; break; } // Если встречаем перевод строки, выходим из цикла

      i++;
    }
    
    Serial.println(queryFromServer); // печатаем в вывод

    if (strncmp("GET / ", queryFromServer, 6) == 0) {
      getSensorData();
    } else if (strncmp("GET /?", queryFromServer, 6) == 0) {
      getQueryString(queryFromServer);
      parseParams(queryFromServer);
      char *a = getParam("mode");
      
      //if (strncmp("mode=searching", queryFromServer, 14) == 0) { answer4serching(); }
      if (strncmp("searching", a, 9) == 0) { answer4serching(); }
      if (strncmp("setting", a, 7) == 0) { setConfigOptions(); }
    }

    Serial.print("Closing connection ...");
    // если подключён клиент
    if (client.connected()) {
      client.println(okHeader);
      client.println(jsonResponse);
      client.flush();
      client.stop();
    }
    
    Serial.println("\n\n");
  }
}

// парсид входящую строку в массив params[] и устанавливает parsedParam в количество прочитанных элементов
void parseParams(char* inputString) {
  Serial.println("PARSING");
  parsedParams = 0; // пока ничего не напарсили

  char* buffer; // лучше так проверять/пропускать вопросилово
  
  for (buffer = strtok(inputString, "="); buffer != NULL; buffer = strtok(NULL, "=")) {
    // парсим ключ
    params[parsedParams].key = buffer;
    
    // парсим значение
    if ((buffer = strtok(NULL, "&")) != NULL) { params[parsedParams].value = buffer; }
    else return ; // фигня какая-то, ключ есть, а значения нет, прекращаем парсинг
    
    parsedParams++; // отмечаем сколько удалось распарсить
    
    if (parsedParams > MAX_PARAMS - 1) return; // больше нет места куда сохранять парсенное.
  }
}

/**
 * Ищем параметр с заданным именем
 */
char *getParam(char *paramName) {
  for (byte i = 0; i < parsedParams; i++) {
    if (strncmp(paramName, params[i].key, strlen(paramName)) == 0) {
      return params[i].value;
    }
  }

  return "\0";
}

/**
 * Вычленяем строку с параметрами запроса
 */
void getQueryString(char *str) {
  for (int i = 0; i < strlen(str); i++) {
    str[i] = str[6 + i];

    if (str[i] == ' ') { str[i] = '\0'; break; } // Если встречаем перевод строки, выходим из цикла
  }
}

void setup() {
  Serial.begin(57600);
  
  pinMode(pin_REL4, OUTPUT);
  pinMode(pin_REL3, OUTPUT);
  pinMode(pin_REL2, OUTPUT);
  pinMode(pin_REL1, OUTPUT);
  pinMode(pin_KEY, OUTPUT);
  
  myBarometer.init();

  initEthernet();
}

void loop() {
  //getSensorData();
  monitoring();

  if (automode) {
    write2SD();
    autoWork();
  }
}


void getFromServer() {
  if (client.connect(serv, 80)) {
    Serial.println("Connected");
    client.println("GET /search?q=arduino HTTP/1.0");
    client.println();
  } else {
    Serial.println("Connection failed");
  }

  while (client.available()) {
    char c = client.read();

    Serial.print(c);
  }

  if (!client.connected()) {
    Serial.println("Disconnecting");
    client.stop();
    while (true);
  }
}


// Выводит в Serial массив parsedParams[]
void printParams() {
  for (byte i = 0; i < parsedParams; i++) {
      Serial.print("key \"");
      Serial.print(params[i].key);
      Serial.print("\" is \"");
      Serial.print(params[i].value);
      Serial.println("\"");
  }
}
