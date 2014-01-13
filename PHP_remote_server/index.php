<?php

// get a sensor if it is listed in $SENSORS_ANALOG_READ or $SENSORS_DIGITAL_READ in sending script
if(isset($_REQUEST['A0']))$A0=$_REQUEST['A0'];
else $A0='?';

// get all stuff I have previously sent with save <name> <value>

if(isset($_REQUEST['saved_states'])) {
	$savedStatesRaw=explode("\n",$_REQUEST['saved_states']);
	$savedStates=array();
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
	$commands.='do set a 11'.(intVal($A0)*255/1023)."\n";

}
echo($commands);
print_r($_REQUEST); // debug stuff
?>