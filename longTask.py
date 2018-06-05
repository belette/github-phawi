#!/usr/bin/env python
# -*- coding: utf-8  -*-
import time
import os

#print('Kill longtask \n')
#os.system('sudo killall python')
#time.sleep(3)

while True:
                cmd = 'sudo php /var/www/html/longTask.php'
                os.system(cmd)
                time.sleep(0.2)
                print