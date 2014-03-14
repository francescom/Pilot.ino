#!/usr/bin/python

import time

# Import pilotino module
from pilotino import Pilotino

serPPort=Pilotino('/dev/ttymxc3',115200)
time.sleep(.5)
serPPort.pinDir("o","10")
if serPPort.isConnected():
	for x in range(0, 50):
		sensor=serPPort.pinGet("a","A0")
		serPPort.pinSet("d","10","hi")
		time.sleep(.5)
		serPPort.pinSet("d","10","lo")
		time.sleep(.5)
		print(sensor)
		
serPPort.disconnect()

