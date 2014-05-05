<?php
	$SERIAL_SPEED=115200; // or 19200 57600 115200 same as SERIAL_SPEED constant in pilot.ino

	require('pilotino.php');
	$UDOOArduino=new PilotIno('/dev/ttymxc3',$SERIAL_SPEED);

	if(isset($_REQUEST['cmd'])) {
		$pin_cmd=$_REQUEST['cmd'];
		$pin_cmd=explode('_',$pin_cmd);
		if(count($pin_cmd)!=2) {
			die("Wrong command, expecting <pin>_<hi/lo>");
		} else {
			$pin=$pin_cmd[0];
			$cmd=$pin_cmd[1];
			
			// test for acceptable pin values here 1..n + A0..An
			// test for acceptable cmd values here hi or lo
			
			
			echo('<div>Now setting pin '.$pin.' value to '.$cmd.'</div>');
			
			$UDOOArduino->dir('out',$pin);
			$didReturnTrue=$UDOOArduino->set('d',$pin,$cmd);
			
			if(!$didReturnTrue) {
				echo("Pilot.ino set command failed");
			}
			
		}
	}
			
?>
<!DOCTYPE html>
<html lang="it">
<head>
	<meta charset="utf-8" />
	<title>Pilotino Test</title>
</head>
<body>
	This is a sample HTML page that light up a LED and reads a sensor.
	
	<div>12 <a href="?cmd=12_on">ON</a> or <a href="?cmd=12_off">OFF</a></div>
	<div>13 <a href="?cmd=13_on">ON</a> or <a href="?cmd=13_off">OFF</a></div>
	<div>14 <a href="?cmd=13_on">ON</a> or <a href="?cmd=13_off">OFF</a></div>
	
	Done!
	Sensor value is: <b>
<?php
        $sensor=$UDOOArduino->get('a','A0');
        echo($sensor);
?>
</b>
</body>
</html>