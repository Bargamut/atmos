/*
  AtmosParams.h - Library for Atmos.
  Created by Alexey Petrov, December 12, 2014.
  Released into the public domain.
*/

#ifndef AtmosParams_h
#define AtmosParams_h

#include <Arduino.h>

class AtmosParams
{
  public:
    AtmosParams();
    void parseParams(char* inputString);
	char *getParam(char *paramName);
	void getQueryString(char *str);
};

#endif