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


		