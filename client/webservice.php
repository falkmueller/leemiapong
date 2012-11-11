<?php

//Wenn Anker mitgeliefert, dann lade Seiteninhalt
if (isset($_POST["hash"])){
	echo GetContentFromHash($_POST["hash"]);
}

//Wenn Anfrage "SearchPlayer", dann Endlosschleife ausführen, bis gegener gefunden (long polling)
if (isset($_POST["SearchPlayer"]) and isset($_POST["key"])){
	$Gamekey = false;
	
	while (!$Gamekey) {
		$Gamekey = CheckOpenGames($_POST["key"]);
	}
	
	echo $Gamekey;
}

/*
 * Funktion gilt einen SPiel Key zurück, wenn ein Gegner gefunden ist
 */
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
				//wenn Spieler sich 5 Sekunden nicht gemeldet hat, dann hat er die endlos Gener-Such-Schleife abgebrochen
				unset($_SESSION["Games"][$tempkey]);
			} else {
				if ($Game["Players"] == 1){
					//wenn ein offenes Spiel vorhanden, dann benutze dies
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

/*
 * importiert je nach übergebenen Hash, die jeweilige Seite, welche der Browser angefordert hat 
 */
function GetContentFromHash($hash){
	if (!(strpos($hash, "#") === false)){
		if (strpos($hash, "#") == 0){
			$hash = substr($hash, 1);
		}
	}
	$hash = "#".$hash;
	
	 if (preg_match("/#info/i", $hash)){
	 	include 'sites/information.php';
	 } 
	 elseif (preg_match("/#game/i", $hash)){
	 	include 'sites/client.php';
	 } 
	 else {
	 	include 'sites/start.php';
	 }
}

/*
 * gibt zufällige Zeichenfolge zurück
 */
function CreateKey(){
	$secret = "";
    $possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    	
    for ($i = 0; $i < 8; $i++) {
    	$secret .= $possible[rand(0, strlen($possible)-1)];
    }
    	
    return $secret;
}

/*
 * Gibt URL derGame-API zurück
 */
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

 	return $pageURL;
}

		