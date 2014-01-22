<?php


class PilotinoReceiver {
	public $SENSORS_ANALOG_READ=array();
	public $SENSORS_DIGITAL_READ=array();
	public $sensors=array();
	public $LINE_SEPARATOR="\n";
	public $savedStates=array();
	public $previousResults;
	public $queuedCommands=array();
        
	function dir($inOut,$pin) {
			if($inOut!='in') $inOut='out';
			else $inOut='in';
			return ($this->sendCmd('dir '.$inOut.' '.$pin))==='OK';
	}
	function set($digAnag,$pin,$val) {
			if($digAnag!='d') $digAnag='a';
			else $digAnag='d';
			return ($this->sendCmd('set '.$digAnag.' '.$pin.' '.$val)==='OK'); 
	}
	function get($digAnag,$pin) {
			if($digAnag!='d') $digAnag='a';
			else $digAnag='d';
			return $this->sendCmd('get '.$digAnag.' '.$pin);
	}
	function sendCmd($cmd,$timeout='') {
		$this->queuedCommands[]='do '.$cmd;
	}
	function saveState($key,$val='',$updateLocal=FALSE) {
		$this->queuedCommands[]='save '.$key.' '.$val;
		if($updateLocal) {
			$this->savedStates[$key]=$val;
		}
	}
	function getState($key) {
		if(isset($this->savedStates[$key])) return $this->savedStates[$key];
		else return null;
	}
	function isState($key,$val=null) {
		if($val==null) return (isset($this->savedStates[$key]));
		else return $this->getState($key)===$val;
	}

	function getSensor($key) {
		if(isset($this->sensors[$key])) return $this->sensors[$key];
		else return '?';
	}
	function isSensor($key,$val=null) {
		if($val==null) return (isset($this->sensors[$key]) && $this->sensors[$key]!='?');
		else return $this->getSensor($key)===$val;
	}

	function __construct($SENSORS_ANALOG_READ,$SENSORS_DIGITAL_READ,$LINE_SEPARATOR) {
		$this->SENSORS_ANALOG_READ=$SENSORS_ANALOG_READ;
		$this->SENSORS_DIGITAL_READ=$SENSORS_DIGITAL_READ;
		$this->LINE_SEPARATOR=$LINE_SEPARATOR;
	}

	function parseRequest(&$aRequest) {
		$this->sensors=array();
		// get a sensor if it is listed in $SENSORS_ANALOG_READ or $SENSORS_DIGITAL_READ

		foreach($this->SENSORS_ANALOG_READ as $aSens) {
			if(isset($aRequest[$aSens])) $this->sensors[$aSens]=$aRequest[$aSens];
			else $this->sensors[$aSens]='?';
		}

		// get all stuff I have previously sent with save <name> <value>
		// Useful to know, for example if board is reset (must init values on it)

		if(isset($aRequest['saved_states'])) {
			$savedStatesRaw=explode("\n",$aRequest['saved_states']);
			$this->savedStates=array();
			foreach($savedStatesRaw as $aState) {
				$aState=explode('=',$aState);
				if(count($aState)>1) {
					$this->savedStates[$aState[0]]=$aState[1];
				} else $this->savedStates[$aState[0]]='';
			}
		} else $this->savedStates=array();

		// get all results of previously sent commands (for sensors use $SENSORS_ANALOG_READ or $SENSORS_DIGITAL_READ)

		if(isset($aRequest['previous_results'])) $this->previousResults=explode($this->LINE_SEPARATOR,$aRequest['previous_results']);
		else $this->previousResults=array();
	}
	function dumpAllAndClose($returnNotEcho=FALSE) {
		$outCmds='';
		print_r($this->queuedCommands);
		foreach($this->queuedCommands as $aCmd) {
			$outCmds.=$aCmd.$this->LINE_SEPARATOR;
		}
		if($returnNotEcho) return $outCmds;
		else echo($outCmds);
	}
}



?>
