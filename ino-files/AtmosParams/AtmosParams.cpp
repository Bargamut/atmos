/*
  AtmosParams.cpp - Library for Atmos.
  Created by Alexey Petrov, December 12, 2014.
  Released into the public domain.
*/

#include "AtmosParams.h"

// —труктра описывающие наш параметр
struct key_param_t{
  char *key;
  char *value;
};

#define MAX_PARAMS 20 			// сколько параметров максимально мы умеем парсить
key_param_t params[MAX_PARAMS]; // в этот массис будем сохран€ть наши парсенные параметры
byte parsedParams = 0; 			// сколько параметров нам удалось напарсить

AtmosParams::AtmosParams() {}

/**
 * ѕарсит вход€щую строку в массив params[]
 * и устанавливает parsedParam в количество прочитанных элементов
 */
void AtmosParams::parseParams(char* inputString) {
  parsedParams = 0; // пока ничего не напарсили

  char* buffer; // лучше так провер€ть/пропускать вопросилово

  for (buffer = strtok(inputString, "="); buffer != NULL; buffer = strtok(NULL, "=")) {
    // парсим ключ
    params[parsedParams].key = buffer;

    // парсим значение
    if ((buffer = strtok(NULL, "&")) != NULL) { params[parsedParams].value = buffer; }
    else return ; // фигн€ кака€-то, ключ есть, а значени€ нет, прекращаем парсинг

    parsedParams++; // отмечаем сколько удалось распарсить

    if (parsedParams > MAX_PARAMS - 1) return; // больше нет места куда сохран€ть парсенное.
  }
}

/**
 * »щем параметр с заданным именем
 */
char *AtmosParams::getParam(char *paramName) {
  for (byte i = 0; i < parsedParams; i++) {
    if (strncmp(paramName, params[i].key, strlen(paramName)) == 0) {
      return params[i].value;
    }
  }

  return "\0";
}

/**
 * ¬ычлен€ем строку с параметрами запроса
 */
void AtmosParams::getQueryString(char *str) {
  for (int i = 0; i < strlen(str); i++) {
    str[i] = str[6 + i];

    if (str[i] == ' ') { str[i] = '\0'; break; } // ≈сли встречаем перевод строки, выходим из цикла
  }
}