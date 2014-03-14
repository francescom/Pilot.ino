<?php

	define('DB_SERVER','your.db.server');
	define('DB_DATABASE','yourdatabase');
	
	define('DB_SERVER_USERNAME','your_username');
	define('DB_SERVER_PASSWORD','yourp4ssw0rdhaha');


////////////////////////////////////////

$UPLOAD_DIR=C_O.'upload/';

define('LOCAL_IMG_PATH',$UPLOAD_DIR);

	
	
function mystripslashes($txt) {
	return stripslashes($txt);
}

function myhtmlentities($txt) {
	return htmlentities($txt,ENT_COMPAT,'UTF-8');
}

function mycontenttype($txt='') {
	header('Content-Type: text/html; charset=UTF-8');
}

function LinkEnt($url) {
	return htmlentities($url);
}


?>