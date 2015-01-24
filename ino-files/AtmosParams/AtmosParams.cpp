/*
  AtmosParams.cpp - Library for Atmos.
  Created by Alexey Petrov, December 12, 2014.
  Released into the public domain.
*/

#include "AtmosParams.h"

// �������� ����������� ��� ��������
struct key_param_t{
  char *key;
  char *value;
};

#define MAX_PARAMS 20 			// ������� ���������� ����������� �� ����� �������
key_param_t params[MAX_PARAMS]; // � ���� ������ ����� ��������� ���� ��������� ���������
byte parsedParams = 0; 			// ������� ���������� ��� ������� ���������

AtmosParams::AtmosParams() {}

/**
 * ������ �������� ������ � ������ params[]
 * � ������������� parsedParam � ���������� ����������� ���������
 */
void AtmosParams::parseParams(char* inputString) {
  parsedParams = 0; // ���� ������ �� ���������

  char* buffer; // ����� ��� ���������/���������� �����������

  for (buffer = strtok(inputString, "="); buffer != NULL; buffer = strtok(NULL, "=")) {
    // ������ ����
    params[parsedParams].key = buffer;

    // ������ ��������
    if ((buffer = strtok(NULL, "&")) != NULL) { params[parsedParams].value = buffer; }
    else return ; // ����� �����-��, ���� ����, � �������� ���, ���������� �������

    parsedParams++; // �������� ������� ������� ����������

    if (parsedParams > MAX_PARAMS - 1) return; // ������ ��� ����� ���� ��������� ���������.
  }
}

/**
 * ���� �������� � �������� ������
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
 * ��������� ������ � ����������� �������
 */
void AtmosParams::getQueryString(char *str) {
  for (int i = 0; i < strlen(str); i++) {
    str[i] = str[6 + i];

    if (str[i] == ' ') { str[i] = '\0'; break; } // ���� ��������� ������� ������, ������� �� �����
  }
}