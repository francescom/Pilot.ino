#!/usr/bin/python

# remember to install serial library
# http://pyserial.sourceforge.net/pyserial.html#from-pypi
# Use "From source" "For Python 3.x:" method if Python 3


import serial
import time

class Pilotino:
	'Class to interface to pilot.ino script on Arduino over serial port'

	def __init__(self,port,speed):
		'Serial port init and open'
		self.serPPort=False
		try:
			self.serPPort = serial.Serial(port,speed,serial.EIGHTBITS,serial.PARITY_NONE,serial.STOPBITS_ONE,.1,False,False,.1,False)
			self.serPPort.timeout=.1
			self.serPPort.writeTimeout=.1
			self.isOpen=False
			try:
				self.serPPort.close()
				self.serPPort.open()
				self.isOpen=True;
			except Exception:
				print("error opening serial port.")
				self.serPPort=False;
		except Exception:
			print("error creating serial port.")
			self.serPPort = False;
			return;

	def isConnected(self):
		'Test if serial is ready'
		if(self.serPPort==False):
			return False
		return self.isOpen;

	def disconnect(self):
		'Test if serial is ready'
		if(self.serPPort==False):
			return False
		if self.isOpen:
			self.serPPort.close()
			self.isOpen=False
		self.serPPort=False
			

	def sendCmd(self,cmd):
		'Send properly formatted command over serial and return response'
		if(self.serPPort==False):
			return False
		response=False;
		dump = self.serPPort.read()
		try:
			self.serPPort.write(cmd+"\n")
			response = self.serPPort.readline()
		except Exception:
			print("Timeout?")
		return response;


	def pinDir(self,pdir,pin):
		'Set pin direction i=input o=output: pinDir(port,"i",10)'
		if pdir!="i" and pdir!="in":
			pdir="o"
		return "OK\n"==self.sendCmd("dir "+pdir+" "+pin);

	def pinGet(self,doa,pin):
		'Get pin value d=digital a=analog/PWM: pinGet(port,"a","A0")'
		if doa!="d" and doa!="D":
			doa="a"
		res=self.sendCmd("get "+doa+" "+pin)
		if(res):
			return res.rstrip("\n");
		else:
			return res

	def pinSet(self,doa,pin,val):
		'Set pin value d=digital a=analog/PWM: pinSet(port,"a","A0",512) // half value PWM'
		if doa!="d" and doa!="D":
			doa="a"
		return "OK\n"==self.sendCmd("set "+doa+" "+pin+" "+val);

