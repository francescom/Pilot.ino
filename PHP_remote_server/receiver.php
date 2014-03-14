<?php


// What is this?


// Arduino -> serial -> local PHP script -> <firewall> -> public web -> public www PHP script (this)


// Example script that waits, receives sensor info and sends commands to execute to a remote PC
// or development board connected to an Arduino

// The usage scenario is a development board or small PC connected to the internet from
// inside a protected LAN with NAT or firewall. The small PC can only send GET/POST requests out
// it can not be reached from the outside world.

// Developed for UDOO board (Linux+Arduino embedded)

// The upload script runs on the small PC and connects to this sample script on some public web server
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




// PARAMETERS

$SENSORS_ANALOG_READ=array('A0'); // receive at every call (quicker than sending a get command, and waiting for results)
$SENSORS_DIGITAL_READ=array(); // receive at every call
$LINE_SEPARATOR="\n"; // Used by remote script to separate commands


// STARTUP

ini_set ('error_reporting' ,2047); 
ini_set ('display_errors' ,"1"); 
ini_set ('display_startup_errors' ,"1"); 

define('C_O','');
$THIS_PAGE=$_SERVER['PHP_SELF'];


////////////////////////////////////////

define('IMG_RESIZER',C_O.'iw/irs.php');


require(C_O.'local_inc/setup.php');
require(C_O.'inc/MyDB.php');
// require(C_O.'inc/Vars.php');
// require(C_O.'inc/SVars.php');


////////////////////////////////////////

$db=new MyDB();
$db->startConnectionArea();


include('pilotino_remote.php');

$remoteArduino=new PilotinoReceiver($SENSORS_ANALOG_READ,$SENSORS_DIGITAL_READ,$LINE_SEPARATOR);
$remoteArduino->parseRequest($_REQUEST);
$timeStampStr=str_replace('.','',''.microtime(TRUE)*1000);



if(!$remoteArduino->isState('inited')) {
	$remoteArduino->dir('out',8);
	$remoteArduino->dir('out',11);
	$remoteArduino->saveState('inited',$timeStampStr);
}

// example toggling a led
if(!$remoteArduino->isState('toggle') || $remoteArduino->getState('toggle')=='0') {
	$remoteArduino->saveState('toggle','1');
	$remoteArduino->set('d',8,'hi');
} else {
	$remoteArduino->saveState('toggle','0');
	$remoteArduino->set('d',8,'lo');
}

if($remoteArduino->isSensor('A0')) {
	$remoteArduino->set('a',13,linearizeLed($remoteArduino->getSensor('A0')*255/1023));
	
	$db->insertQuery('arduino_sensors',$InfoArray,$Replace=FALSE);
	
	
}

foreach($remoteArduino->sensors as $aSensor=>$aSensorVal) {
	
	$sql=$db->insertQuery('arduino_sensors',array(
		'timestamp'=>time(),
		'board_id'=>'board_1', // this should come from the uploading machine
		'sensor_name'=>preg_replace('/[?A-Z0-9_-]/','',$aSensor),
		'sensor_value'=>preg_replace('/[?A-Z0-9_-]/','',$aSensorVal),
		'other_data_1'=>'',
		'other_data_2'=>'',
	),FALSE);
	$db->query($sql);
	$db->query('delete from `arduino_sensors` where timestamp<'.(time()-60*60));
	
}
$cmds=$db->collect('arduino_commands','`order`,`command_text`',array('board_id'=>'board_1'),'','`timestamp`,`order`','');

// yes here some mild things may happen (like you light a let twice)

$db->delete('arduino_commands',array('board_id'=>'board_1'),'','');

// yes here some very bad things may happen (like you do not start your oxygen making life support system)


$remoteArduino->dumpAllAndClose();

// All other echo can be printed on the local script to debug
echo("\nREQUEST: "); // debug stuff
print_r($_REQUEST); // debug stuff
// echo("\nSAVED STATES: "); // debug stuff
// print_r($savedStates); // debug stuff
// echo("\nRESULTS: "); // debug stuff
// print_r($previousResults); // debug stuff

function linearizeLed($x) {
	// http://www.picbasic.co.uk/forum/showthread.php?t=16187
	$gamma=0.75;
	// return intVal(round((255^((($x+1)/255)^gamma)+.3)));
	
	// http://electronics.stackexchange.com/questions/1983/correcting-for-non-linear-brightness-in-leds-when-using-pwm	
	return intVal(round(1/(1+exp((($x/21)-6)*-1))*255));
}
?>
