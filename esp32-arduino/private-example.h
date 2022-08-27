
//image size is width x height / 8
#define IMAGE_SIZE 38880

//URLs of bin.php and config.php on some webserver
#define IMAGE_DATA_URL "http://www.example-webserver.net/photoFrame/bin.php?frameID=stechow"
#define CONFIG_URL "http://www.example-webserver.net/photoFrame/config.php?frameID=stechow"

//primary wifi, e.g. in the home of the target
#define WIFI_SSID1 "TheirWifiSSID"
#define WIFI_PASS1 "theirpassword"

//backup wifi, e.g. in the home of the builder
#define WIFI_SSID2 "MyWiFiSSID"
#define WIFI_PASS2 "mypassword"

//which screen to use, EPD_5IN83B_V2 for the 648x480 and EPD_5IN3BC for the 600x488
#define EPD_WIDTH EPD_5IN83B_V2_WIDTH
#define EPD_HEIGHT EPD_5IN83B_V2_HEIGHT
#define EPD_Init EPD_5IN83B_V2_Init
#define EPD_Clear EPD_5IN83B_V2_Clear
#define EPD_Display EPD_5IN83B_V2_Display
#define EPD_Sleep EPD_5IN83B_V2_Sleep


