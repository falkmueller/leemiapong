<?php

if (isset($_POST["hash"])){
	echo GetContentFromHash($_POST["hash"]);
}

function GetContentFromHash($hash){
	if (!(strpos($hash, "#") === false)){
		if (strpos($hash, "#") == 0){
			$hash = substr($hash, 1);
		}
	}
	
	$url = parse_url($hash);
	
	$Page=$url["path"];
	$Parameter = array();
	
	if (isset($url["query"])) {
	$Parameter = $url["query"];
	}
	
	 if ($Page == "info"){
	 	include 'sites/information.php';
	 } elseif ($Page == "game" and isset($Parameter["key"]) and isset($Parameter["name"]))
	 {
	 	include 'sites/client.php';
	 } else {
	 	include 'sites/start.php';
	 }
}

function CreateKey(){
		$secret = "";
    	$possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    	
    	for ($i = 0; $i < 8; $i++) {
    		$secret .= $possible[rand(0, strlen($possible))];
    	}
    	
    	return $secret;
	}
	
function WSURL() {
 $pageURL = 'http';
 if (isset($_SERVER["HTTPS"])) {if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 
 $pageURL = substr($pageURL, 0, strpos($pageURL, "/client"));
 $pageURL = $pageURL."/game";
 return $pageURL;
}

		