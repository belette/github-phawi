#include "RCSwitch.h"
#include <stdlib.h>
#include <stdio.h>
#include <unistd.h>
#include <stdlib.h>
#include <string.h>     
RCSwitch mySwitch;

int main(int argc, char *argv[]) {

     int PIN =21;
     int ret;
     char cmdfinale[43];
    memset(cmdfinale, '\0', sizeof(cmdfinale));

     if(wiringPiSetup() == -1)
       return 0;
        mySwitch = RCSwitch();
        mySwitch.enableReceive(PIN);   

     while(1) {
        ret = system("sudo php /var/www/getRFbedroom.php");
        printf("done ");
        if (mySwitch.available()) {
            int value = mySwitch.getReceivedValue();
            if (value == 0) {  
                printf("Unknown encoding\n");
            } else {  
                unsigned long int i = mySwitch.getReceivedValue();
                sprintf(cmdfinale , "sudo php /var/www/getRFbedroom.php %lu", i);
                ret = system(cmdfinale);
            }
            mySwitch.resetAvailable(); 
        }
    }
   exit(0); 
}

