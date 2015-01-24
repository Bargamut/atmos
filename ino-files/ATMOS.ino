////////////////////////////////////////////////
//                                            //
// ATMOS                                      //
//                                            //
////////////////////////////////////////////////

#include <SPI.h>
#include <Ethernet.h>
#include <Barometer.h>
#include <Wire.h>
#include <AtmosParams.h>

// настройки пинов
#define pin_GHUM A0  // Влажность почвы
#define pin_LDR A1   // LDR-датчик
#define pin_REL1 8   // Реле №1: свет
#define pin_REL2 6   // Реле №2: вода
#define pin_REL3 5   // Реле №3: тепло
#define pin_REL4 2   // Реле №4: < ... >
#define SD_PIN 4     // SD-карта
#define pin_KEY 3    // Силовой ключ (отопление, свет)

#define jsonBuf 150

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
int HUMIDY_MIN            = 20;    // настройки для Влажности почвы
int HUMIDY_MAX            = 700;
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
char jsonResponse[jsonBuf];

EthernetServer server = EthernetServer(80); // Создаём Ethernet-сервер, ожидающий соединения на 80 порт
EthernetClient client;                      // Создаём клиента, для соединения с указанным IP-адресом
Barometer myBarometer;                      // Барометр
AtmosParams atmos;                          // Параметры для обработки запроса

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

/**
 * Ответ на опрос устройств, высылаем свой локальный IP в сети
 */
void answer4serching() {
  union IPAddressConverter {
    uint32_t ipInteger;
    uint8_t ipArray[4];
  };

  IPAddressConverter ipAddress;
  ipAddress.ipInteger = Ethernet.localIP();

  sprintf(jsonResponse, "{\"device_ip\": \"%d.%d.%d.%d\"}", ipAddress.ipArray[0], ipAddress.ipArray[1], ipAddress.ipArray[2], ipAddress.ipArray[3]);
}

/**
 * Опрос датчиков, обновление данных
 */
static void getSensorData() {
  LIGHTNESS    = askLDR();
  TEMPERATURE  = askTemperature();
  PRESSURE     = askPressure();
  ALTITUDE     = askAltitude();
  G_HUMIDY     = askHumidy();
  
  sprintf(jsonResponse,
    "{"
      "\"lightness\": %d,"
      "\"temperature\": %d,"
      "\"pressure\": %ld,"
      "\"altitude\": %d,"
      "\"g_humidy\": %d"
    "}",
    LIGHTNESS,
    TEMPERATURE,
    PRESSURE,
    ALTITUDE,
    G_HUMIDY);
}

/**
 * Запрос значений конфурации
 */
void getConfigOptions() {
  sprintf(jsonResponse,
    "{"
      "\"reconnect\": %u,"
      "\"light_min\": %d,"
      "\"temp_min\": %d,"
      "\"hum_min\": %d,"
      "\"hum_max\": %d,"
      "\"wait\": %u,"
      "\"watering\": %u"
    "}",
    reconnectTimeout,
    LIGHTNESS_MIN,
    TEMPERATURE_MIN,
    HUMIDY_MIN,
    HUMIDY_MAX,
    timeWait,
    timeWatering
  );
}

/**
 * Установка новых значений конфурации
 */
void setConfigOptions() {
  sprintf(jsonResponse, "{\"status\": \"true\"}");
}

void monitoring() {
  // при подсоединении клиента появляются байты доступные для чтения:
  client = server.available(); // если есть в клиенте непрочитанные байты, то возвращаем в client объект, его описывающий

  if (client.available()) {
    char queryFromServer[client.available()];
    int i = 0;

    memset(jsonResponse, '\0', jsonBuf);

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
      atmos.getQueryString(queryFromServer);
      atmos.parseParams(queryFromServer);
      char *a = atmos.getParam("mode");
      
      Serial.println(a); 
      
      if (strncmp("searching", a, 9) == 0)      { Serial.println("searching"); answer4serching(); }
      if (strncmp("setting", a, 7) == 0)        { Serial.println("setting"); setConfigOptions(); }
      if (strncmp("getdef", a, 6) == 0)         { Serial.println("getdef"); getConfigOptions(); }
      if (strncmp("manipulation", a, 12) == 0)  { Serial.println("manipulation"); }
    }

    Serial.print("Connection ...");
    // если подключён клиент
    if (client.connected()) {
      Serial.print(" send data ... ");
      Serial.print(okHeader);
      Serial.print(jsonResponse);

      client.println(okHeader);
      client.println(jsonResponse);
      client.flush();
      client.stop();
      Serial.print(" closing ... ");
    }
    Serial.print("end");

    Serial.println("\n\n");
  }
}

/**
 * Спрашиваем освещённость среды
 * Считываем уровень освещённости (0 - свет, 1023 - темнота), обратнопропорционально
 */
int askLDR() { return (1023 - analogRead(pin_LDR)); }

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
  if (G_HUMIDY > HUMIDY_MIN || (wait - def) < (timeWait - timeWatering)) { digitalWrite(pin_REL2, LOW); } // Прекращаем полив
  if (wait != 0 && (wait - def) > 50) { return; } // если время ещё не вышло
  else                                { wait = 0; }

  // если влажность почвы меньше минимума
  if (G_HUMIDY < HUMIDY_MIN) {
    digitalWrite(pin_REL2, HIGH); // Поливаем
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
 * Автоматический контроль данных и управление манипуляторами
 */
void autoWork() {
  watering(); // поливаем
  lighting(); // освещаем
  warming(); // греем
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
    autoWork();
  }
}