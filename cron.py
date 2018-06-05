#!/usr/bin/env python
# -*- coding: utf-8  -*-
import time
import os

print('Start du script Longtask ....  \n')
os.system('sudo bash -c "exec -a phawi python /var/www/html/longTask.py & 2>&1" &')
print('Demarrage effectué  \n')
