/* E-paper Photo frame periodic download and sleep script
   designed for the Waveshare ESP32 board

 Connect to wifi, contact a webserver and download configuration and the next image
 Display the image on the e-paper display
 display an error if something goes wrong
 goto sleep for the time specific by the downloaded configuration

 preSleep: 
    For some reason, the first version of the waveshare board (the one with one voltage regulator) 
    didn't go into full low power when sleeping after doing wifi and image upload. This is worked
    around by sleeping only briefly and then sleeping again immediately after waking up. This 
    somehow succeeds in getting to very low power usage (~35 micro Amps).

    I didn't test if the second version (which has two smaller voltage regulators) does the same.
*/


#include "DEV_Config.h"
#include "EPD.h"
#include "GUI_Paint.h"

#include "driver/rtc_io.h"
#include <WiFi.h>
#include <HTTPClient.h>

//settings (with private data)
//include "private-stechow.h"
//include "private-perseo1.h"
#include "private-example.h"

#undef DEBUG

#ifdef DEBUG
  #define DBG_delay(x) delay(x)
#else
  #define DBG_delay(x) {}
#endif

#define TIME_TO_PRESLEEP  1        /* Time ESP32 will go to pre-sleep (in seconds) */
//int timeToSleep =  240;        /* Time ESP32 will go to sleep (in seconds) */
RTC_DATA_ATTR int timeToSleep = 5*3600;        /* 5 hours */

void check_wakeup();
void downloadImage();
void errorImage(const char *errorString);
void uploadImage();
void goto_sleep(boolean preSleep);

unsigned char image_b[IMAGE_SIZE];
unsigned char image_r[IMAGE_SIZE];

void setup() { 
  //Serial.begin(115200);
  DEV_Module_Init();
  DEV_Delay_ms(500);
  esp_log_level_set("*", ESP_LOG_DEBUG);
  
  //check the reason for waking up and re-enter sleep if we did a preSleep
  Serial.println("chk_wakeup");
  check_wakeup();

  //set the status LED
  Serial.println("DEV_Init");  
  ledSetup();
  setLED(255);
  
  DBG_delay(3000);

  //download the configuration and next image
  downloadImage();

  setLED(64);
  
  //display the image on the e-paper
  uploadImage();
  
  DBG_delay(2000);
  
  //goto sleep (presleep)
  goto_sleep(true);  
}

void loop() {
  // we never get here, there is no loop
}


// ----- wifi control ----


void downloadImage(){
  Serial.println("wifi begin");
  WiFi.begin(WIFI_SSID1, WIFI_PASS1);
  
  // Waiting the connection to a router
  Serial.print("wifi #1:");
  for(int i=0; i < 30; i++){
    delay(250);
    if(WiFi.status() == WL_CONNECTED)
      break;
    Serial.print(".");
  }

  if(WiFi.status() != WL_CONNECTED){
    
    Serial.println("\nwifi #1 failed.");
    WiFi.disconnect();
    delay(500);
    
    WiFi.begin(WIFI_SSID2, WIFI_PASS2);
  
    // Waiting the connection to a router
    Serial.print("wifi #2:");
    for(int i=0; i < 30; i++){
      delay(250);
      if(WiFi.status() == WL_CONNECTED)
        break;
      Serial.print(".");
    }

    if(WiFi.status() != WL_CONNECTED){
      errorImage("WiFi connection timed out");
      WiFi.disconnect();
      WiFi.mode(WIFI_OFF);
      return;
    
    }
    
  }
  
  // Connection is complete
  Serial.println("OK");

  HTTPClient http;

  Serial.println("http begin");
  http.begin(IMAGE_DATA_URL);

  Serial.println("http get"); 
  int httpCode = http.GET();

  if(httpCode == HTTP_CODE_OK) {
    // HTTP header has been send and Server response header has been handled
    int len = http.getSize();
    Serial.printf("Getting %d bytes over HTTP", len);
      
    WiFiClient *stream = http.getStreamPtr();
    int readLen = stream->readBytes(image_b, IMAGE_SIZE);
    Serial.printf("Read %d bytes of image BW\n", readLen);
      
    readLen = stream->readBytes(image_r, IMAGE_SIZE);
    Serial.printf("Read %d bytes of image R\n", readLen);
    
  } else {
    Serial.printf("[HTTP] GET... failed, code: %d, error: %s\n", httpCode, http.errorToString(httpCode).c_str());

    String content = http.getString();
    if(content == NULL)
      content = "NULL";
    
    Serial.printf("content: %s\n", content.c_str());
    errorImage(content.c_str());
  }

  Serial.println("http end");
  http.end();

  
  http.begin(CONFIG_URL);
  httpCode = http.GET();
  String content = http.getString();
  
  if(httpCode == HTTP_CODE_OK) {
    Serial.printf("Got config: %s\n", content);
    int newSleep = content.toInt();
    if(newSleep > 0 && newSleep < 31536000)
      timeToSleep = newSleep;
      
    Serial.printf("timeToSleep = %i\n", timeToSleep);
  }else {
    Serial.printf("[HTTP] config GET ... failed, code: %d, error: %s\n", httpCode, http.errorToString(httpCode).c_str());
    
    if(content == NULL)
      content = "NULL";
    
    Serial.printf("content: %s\n", content.c_str());
    errorImage(content.c_str());
  }

  
  
  http.end();

  Serial.print("wifi disconnect...");
  WiFi.disconnect();
  Serial.println("ok");

  DBG_delay(2000);
  
  Serial.print("wifi off...");
  WiFi.mode(WIFI_OFF);
  Serial.println("ok");

  DBG_delay(2000);
  
}


void errorImage(const char *errorString){
  Serial.printf("errorImage(%s)\r\n", errorString);

  Paint_NewImage(image_b, EPD_WIDTH, EPD_HEIGHT, 0, WHITE);
  Paint_NewImage(image_r, EPD_WIDTH, EPD_HEIGHT, 0, WHITE);

  //Select Image
  Paint_SelectImage(image_b);
  Paint_Clear(WHITE);
  Paint_SelectImage(image_r);
  Paint_Clear(WHITE);

  Paint_DrawString_EN(10, 20, "ERROR getting binary data", &Font12, WHITE, BLACK);
  Paint_DrawString_EN(10, 30, errorString, &Font12, WHITE, BLACK);
  
}


// ----- epaper control -----
void uploadImage(){

  printf("uploadImage()\r\n");

  printf("e-Paper Init and Clear...\r\n");
  EPD_Init();
  EPD_Clear();
  DEV_Delay_ms(100);

  
  printf("show image for array\r\n");
  EPD_Display(image_b, image_r);
  DBG_delay(2000);

  printf("EPD_Sleep...\r\n");
  EPD_Sleep();  

}



// ----- sleep and wakeup control -----
#define uS_TO_S_FACTOR 1000000ULL  /* Conversion factor for micro seconds to seconds */

RTC_DATA_ATTR int bootCount = 0;
RTC_DATA_ATTR boolean isPresleep = false;

void check_wakeup(){
  esp_sleep_wakeup_cause_t wakeup_reason;
  bootCount++;

  Serial.printf("bootCount = %d\n", bootCount);
    
  wakeup_reason = esp_sleep_get_wakeup_cause();
  switch(wakeup_reason)
  {
    case ESP_SLEEP_WAKEUP_EXT0 : Serial.println("Wakeup caused by external signal using RTC_IO"); break;
    case ESP_SLEEP_WAKEUP_EXT1 : Serial.println("Wakeup caused by external signal using RTC_CNTL"); break;
    case ESP_SLEEP_WAKEUP_TIMER : Serial.println("Wakeup caused by timer"); break;
    case ESP_SLEEP_WAKEUP_TOUCHPAD : Serial.println("Wakeup caused by touchpad"); break;
    case ESP_SLEEP_WAKEUP_ULP : Serial.println("Wakeup caused by ULP program"); break;
    default : Serial.printf("Wakeup was not caused by deep sleep: %d\n",wakeup_reason); break;
  }

  if(isPresleep){
    Serial.println("Woke from presleep. Doing full sleep...");
    goto_sleep(false);
  }
}


void goto_sleep(boolean preSleep){

  Serial.print("Sleep: ");
  Serial.println(preSleep);
  isPresleep = preSleep;
  
  /*
  First we configure the wake up source
  We set our ESP32 to wake up for an external trigger.
  There are two types for ESP32, ext0 and ext1 .
  ext0 uses RTC_IO to wakeup thus requires RTC peripherals
  to be on while ext1 uses RTC Controller so doesnt need
  peripherals to be powered on.
  Note that using internal pullups/pulldowns also requires
  RTC peripherals to be turned on.
  */
  Serial.println("RTC config:");
  Serial.println(rtc_gpio_pulldown_dis(GPIO_NUM_33));
  Serial.println(rtc_gpio_pullup_en(GPIO_NUM_33));
  Serial.println(esp_sleep_enable_ext0_wakeup(GPIO_NUM_33, 0)); //1 = High, 0 = Low
  

  /*
  We set our ESP32 to wake up every 5 seconds
  */
  int thisSleep = preSleep ? TIME_TO_PRESLEEP : timeToSleep;
  esp_sleep_enable_timer_wakeup(thisSleep * uS_TO_S_FACTOR);
  Serial.println("Setup ESP32 to sleep for every " + String(timeToSleep) +" Seconds");

  //setLED(0);

  //Go to sleep now
  Serial.println("Going to sleep now\r\n");
  DBG_delay(2000);
  
  esp_deep_sleep_start();
  Serial.println("This will never be printed");

}


// ------ control of the red LED for debugging --------

// use first channel of 16 channels (started from zero)
#define LEDC_CHANNEL_0     0

// use 13 bit precission for LEDC timer
#define LEDC_TIMER_13_BIT  13

// use 5000 Hz as a LEDC base frequency
#define LEDC_BASE_FREQ     5000

// fade LED PIN (replace with LED_BUILTIN constant for built-in LED)
//red onboard LED
#define LED_PIN            2 

int brightness = 0;    // how bright the LED is
int fadeAmount = 5;    // how many points to fade the LED by

// Arduino like analogWrite
// value has to be between 0 and valueMax
void ledcAnalogWrite(uint8_t channel, uint32_t value, uint32_t valueMax = 255) {
  // calculate duty, 8191 from 2 ^ 13 - 1
  uint32_t duty = (8191 / valueMax) * min(value, valueMax);

  // write duty to LEDC
  ledcWrite(channel, duty);
}
void ledSetup() {
  // Setup timer and attach timer to a led pin
  ledcSetup(LEDC_CHANNEL_0, LEDC_BASE_FREQ, LEDC_TIMER_13_BIT);
  ledcAttachPin(LED_PIN, LEDC_CHANNEL_0);
}

void setLED(int brightness) {
  // set the brightness on LEDC channel 0
  ledcAnalogWrite(LEDC_CHANNEL_0, brightness);
}
