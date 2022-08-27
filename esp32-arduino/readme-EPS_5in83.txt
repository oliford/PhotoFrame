When I made the second version of this, the 5.83" screen had changed from 600x448 to 648x480 and requires a different communication code.
That wasn't part of the ESP32 library from waveshare at the time, so I made the EPD_5in83b_V2.cpp and .h, mostly copied from the RPi Pico code for that screen.
These need to be copied into the ESP32 library in the arduino libraries path and an include line added to the EPD.h

