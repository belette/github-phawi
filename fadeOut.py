#!/usr/bin/python3

import RPi.GPIO as GPIO # Remember to run as superuser (sudo)
import time, sys, getopt
GPIO.setwarnings(False)
GPIO.setmode(GPIO.BCM)   # This example uses the BCM pin numbering
GPIO.setup(13, GPIO.OUT) # GPIO 25 is set to be an output.
GPIO.output(13, GPIO.HIGH)
pwm = GPIO.PWM(13, 50)   # pwm is an object. This gives a neat way to control the pin.
pwm.start(10)            # This 50 is the mark/space ratio or duty cycle of 50%
pwm.ChangeFrequency(40)  # Frequency is now 50 Hz - LED stops flickering
for count in range(0, 100):    # Iterates 0, 1, 2, 3, 4, 5, 6, 7, 8, 9 - Never reaches 10!
        pwm.ChangeDutyCycle(100-count)
        time.sleep(0.003)
GPIO.output(13, GPIO.LOW) 