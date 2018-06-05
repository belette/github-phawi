#!/usr/bin/python
import RPi.GPIO as GPIO
import sys, time
gpio=int(sys.argv[1])
GPIO.setmode(GPIO.BCM)
GPIO.setup(gpio, GPIO.OUT)
print(GPIO.input(gpio))
pwm = GPIO.PWM(gpio, 50)   # pwm is an object. This gives a neat way to control the pin.
pwm.start(10)            # This 50 is the mark/space ratio or duty cycle of 50%
pwm.ChangeFrequency(40)  # Frequency is now 50 Hz - LED stops flickering

if int(sys.argv[2]) == 1:
    GPIO.output(gpio, GPIO.LOW)
    for count in range(0, 50):
        pwm.ChangeDutyCycle(count*2)
        time.sleep(0.003)
    GPIO.output(gpio, GPIO.HIGH)

if int(sys.argv[2]) == 0:
    GPIO.output(gpio, GPIO.HIGH)
    for count in range(0, 50):
        pwm.ChangeDutyCycle(100-(count*2))
        time.sleep(0.003)
    GPIO.output(gpio, GPIO.LOW)
pwm.stop()
GPIO.cleanup()