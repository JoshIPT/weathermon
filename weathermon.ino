#include <Average.h>
#include <FreqMeasure.h>

/* SENSOR ID */
#define SENSORID 1

int sensorPin = A0;
int voltPin = A1;
float sensorValue = 0.000;
int sampleNum = 0;
int sampleMax = 5000;
float actualValue = 0.000;
Average<float> windvals(30);
String winddir = "";
float windspeed = 0.00;
float hertz = 0.00;
float gustspeedhz = 0.00;
float gustspeed = 0.00;

int windSpeed_updated = 0;

#define _N 2.33
#define _NE 1.11
#define _E 1.48
#define _SE 1.26
#define _S 3.14
#define _SW 2.62
#define _W 3.77
#define _NW 2.03

void setup() {
  Serial.begin(57600);
  //Serial.println("TPT Wind speed + direction monitor");
  FreqMeasure.begin();
}

double sum=0;
int count=0;
long loopstart = millis();

void loop() {

  if (FreqMeasure.available()) {
    double thisread = FreqMeasure.read();
    sum = sum + thisread;
    if ((FreqMeasure.countToFrequency(thisread) * 2.197598) > gustspeed) {
      gustspeedhz = thisread;
      gustspeed = FreqMeasure.countToFrequency(gustspeedhz) * 2.197598;
    }
    windvals.push(analogRead(sensorPin));
    count = count + 1;
    
    if (count > 30) {

      float frequency = FreqMeasure.countToFrequency(sum / count);

      float kph = frequency * 2.197598;
      windspeed = kph;
      hertz = frequency;

      actualValue = windvals.mode() * (5 / 1023.0);
      if ((actualValue > (_N - 0.15)) && (actualValue < (_N + 0.15))) { winddir = "North"; }
      else if ((actualValue > (_NE - 0.15)) && (actualValue < (_NE + 0.15))) { winddir = "North-East"; }
      else if ((actualValue > (_E - 0.15)) && (actualValue < (_E + 0.15))) { winddir = "East"; }
      else if ((actualValue > (_SE - 0.15)) && (actualValue < (_SE + 0.15))) { winddir = "South-East"; }
      else if ((actualValue > (_S - 0.15)) && (actualValue < (_S + 0.15))) { winddir = "South"; }
      else if ((actualValue > (_SW - 0.15)) && (actualValue < (_SW + 0.15))) { winddir = "South-West"; }
      else if ((actualValue > (_W - 0.15)) && (actualValue < (_W + 0.15))) { winddir = "West"; }
      else if ((actualValue > (_NW - 0.15)) && (actualValue < (_NW + 0.15))) { winddir = "North-West"; }

      Serial.print(SENSORID);
      Serial.print(",");
      Serial.print(hertz);
      Serial.print(",");
      Serial.print(windspeed);
      Serial.print(",");
      Serial.print(FreqMeasure.countToFrequency(gustspeedhz));
      Serial.print(",");
      Serial.print(gustspeed);
      Serial.print(",");
      Serial.print(actualValue);
      Serial.print(",");
      if (winddir == "") { Serial.println("\"\""); }
      else { Serial.println(winddir); }

      
      sum = 0;
      count = 0;
      gustspeed = 0.00;
      gustspeedhz = 0.00;
    }
  }
}
