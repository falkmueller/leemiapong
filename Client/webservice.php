<?php

if (isset($_POST["hash"])){
	echo GetContentFromHash($_POST["hash"]);
}

if (isset($_POST["SearchPlayer"]) and isset($_POST["key"])){
	$Gamekey = false;
	while (!$Gamekey) {
		$Gamekey = CheckOpenGames($_POST["key"]);
	}
	
	echo $Gamekey;
}

function CheckOpenGames($key){
	session_id("-StaticOneSession");
	session_start();
		
	if (!isset($_SESSION["Games"])){
		$_SESSION["Games"] = array();
	}
	
	//wenn zu meinem Key ein Gegenspieler gefunden
	if (isset($_SESSION["Games"][$key])){
		$_SESSION["Games"][$key]["Time"] = time();
		if ($_SESSION["Games"][$key]["Players"] == 2){
			return $key;
		}
	} 
	else {
		//gibt es ein offenes Spiel
		foreach ($_SESSION["Games"] as $tempkey => $Game){ 
			if ($Game["Time"] < time() - 5){
				unset($_SESSION["Games"][$tempkey]);
			} else {
				if ($Game["Players"] == 1){
					$_SESSION["Games"][$tempkey]["Players"] = 2;
					return $tempkey;
				}
			}
		}
		
		//selber Spiel eröffnen und auf Gegner Warten
		$_SESSION["Games"][$key] = array("Players" => 1, "Time" => time());
	}
	
	session_write_close();
	return false;
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
    		$secret .= $possible[rand(0, strlen($possible)-1)];
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

		