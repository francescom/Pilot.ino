#!/usr/bin/python
import serial
import time

# >>> THIS IS UNTESTED CODE


def initPilot(port,speed):
	'Serial port init and open'
	serPilotPort=False
	try:
		serPilotPort = serial.Serial(port,speed,serial.EIGHTBITS,serial.PARITY_NONE,serial.STOPBITS_ONE,.1,False,False,.1,False)
		serPilotPort.timeout=.1
		serPilotPort.writeTimeout=.1
		try:
			serPilotPort.close()
			serPilotPort.open()
		except Exception:
			print("error opening serial port.")
			serPilotPort=False;
			return;
	except Exception:
		print("error creating serial port.")
		serPilotPort=False;
		return;
	return serPilotPort;

def sendCmd(serPilotPort,cmd):
	'Send properly formatted command over serial and return response'
	response=False;
	dump = serPilotPort.read()
	try:
		serPilotPort.write(cmd+"\n")
		response = serPilotPort.readline()
	except Exception:
		print("Timeout?")
	return response;


def pinDir(serPilotPort,dir,pin):
	'Set pin direction i=input o=output: pinDir(port,"i",10)'
	if dir!="i" and dir!="in":
		dir="o"
	return "OK\n"==sendCmd(serPilotPort,"dir "+dir+" "+pin);

def pinGet(serPilotPort,doa,pin):
	'Get pin value d=digital a=analog/PWM: pinGet(port,"a","A0")'
	if doa!="d" and doa!="D":
		doa="a"
		res=sendCmd(serPilotPort,"get "+doa+" "+pin)
		if(res):
			return res.rstrip("\n");
		else:
			return res

def pinSet(serPilotPort,doa,pin,val):
	'Set pin value d=digital a=analog/PWM: pinSet(port,"a","A0",512) // half value PWM'
	if doa!="d" and doa!="D":
		doa="a"
	return "OK\n"==sendCmd(serPilotPort,"set "+doa+" "+pin+" "+val);



# Sample usage:

serPPort=initPilot('/dev/ttymxc3', 115200)
time.sleep(.5)
if serPPort:
	pinDir(serPPort,"o","10")

	for x in range(0, 50):
		sensor=pinGet(serPPort,"a","A0")
		pinSet(serPPort,"d","10","hi")
		time.sleep(.5)
		pinSet(serPPort,"d","10","lo")
		time.sleep(.5)
		print(sensor);


