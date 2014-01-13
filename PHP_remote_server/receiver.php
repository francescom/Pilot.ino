<?php


// What is this?


// Arduino -> serial -> local PHP script -> <firewall> -> public web -> public www PHP script (this)


// Example script that waits, receives sensor info and sends commands to execute to a remote PC
// or development board connected to an Arduino

// The usage scenario is a development board or small PC connected to the internet from
// inside a protected LAN with NAT or firewall. The small PC can only send GET/POST requests out
// it can not be reached from the outside world.

// Developed for UDOO board (linux+Arduino embedded)

// The uploadtest script runs on the small PC and connects to this sample script on some public web server
// The public web server can receive sensor data from the small PC and return commands to execute
// (turn on/off or dim leds, activate motors, etc..)


// THIS PARTICULAR EXAMPLE will

// Init pin direction at first call (send dir command to Arduino if saved state "inited" is not defined)
// Set saved state named "inited" to 1
// Receive sensor A0 value (0-1023) from POST data
// Change a saved state named "toggle" from 0 to 1 or from 1 to 0 alternatively, just because.
// Send to Arduino a command to light a led if saved state named "toggle" is 1, turn off otherwise
// Send to Arduino a command to dim a led to the intensity of the sensor A0 received (from range 0-1023 to range 0-255)

// Practically: get sensor value, return same value to dim led
// (yeah useless ok, but sensor could be in New York and Led in Bangkok, with web server in Rome)



// get a sensor if it is listed in $SENSORS_ANALOG_READ or $SENSORS_DIGITAL_READ in sending script
if(isset($_REQUEST['A0'])) $A0=$_REQUEST['A0'];
else $A0='?';

// get all stuff I have previously sent with save <name> <value>
// Useful to know, for example if board is reset (must init values on it)

if(isset($_REQUEST['saved_states'])) {
	$savedStatesRaw=explode("\n",$_REQUEST['saved_states']);
	$savedStates=array();
	foreach($savedStatesRaw as $aState) {
		$aState=explode('=',$aState);
		if(count($aState)>1) {
			$savedStates[$aState[0]]=$aState[1];
		} else $savedStates[$aState[0]]='';
	}
} else $savedStates=array();

// get all results of previously sent commands (for sensors use $SENSORS_ANALOG_READ or $SENSORS_DIGITAL_READ)

if(isset($_REQUEST['previous_results'])) $previousResults=explode("\n",$_REQUEST['previous_results']);
else $previousResults=array();

$commands='';

if(!isset($savedStates['inited'])) {
	$commands.='do dir out 8'."\n";	
	$commands.='do dir out 11'."\n";	
	$commands.='save inited 1'."\n";	
}

// example toggling a led
if(!isset($savedStates['toggle']) || $savedStates['toggle']=='0') {
	$commands.='save toggle 1'."\n";	
	$commands.='do set d 8 hi'."\n";	
} else {
	$commands.='save toggle 0'."\n";	
	$commands.='do set d 8 lo'."\n";	
}

if(is_numeric($A0)) {
	$commands.='do set a 11 '.(intVal($A0)*255/1023)."\n";
}
echo($commands);

// All other echo can be printed on the local script to debug
echo("\nREQUEST: "); // debug stuff
print_r($_REQUEST); // debug stuff
// echo("\nSAVED STATES: "); // debug stuff
// print_r($savedStates); // debug stuff
// echo("\nRESULTS: "); // debug stuff
// print_r($previousResults); // debug stuff
?>
