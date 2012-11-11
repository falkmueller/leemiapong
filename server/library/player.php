<?php 
/*
 * Behandelt alle Funktionen die mit einen Spieler zu tun haben
 */
class player {

	/*
	 * loggt Spieler in Spiel ein und gibt Secure Key zurück
	 */
	public static function login($Key, $Playername, &$Side = "left") {
		$game = new game();
		$gameObj = sessionManager::$game;
		
		if ($gameObj->players["right"]["SecureKey"] != ""){
			response::$error = true;
			return "Game full";
		} 
		
		$Secret = player::CreateSecret();
		
		if($gameObj->players["left"]["SecureKey"] == ""){
			$gameObj->players["left"]["name"] = $Playername;
			$gameObj->players["left"]["SecureKey"] = $Secret;
			$Side = "left";
		} else {
			$gameObj->players["right"]["name"] = $Playername;
			$gameObj->players["right"]["SecureKey"] = $Secret;
			$Side = "right";
			$gameObj->status = statusEnum::$STATUS_READY;
			if ($gameObj->autoStart){$game->start();}
		}
			
		return $Secret;
	}
	
	/*
	 * Erzeugt zufällige Zeichenfolge
	 */
	private static function CreateSecret(){
		$secret = "";
    	$possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    	
    	for ($i = 0; $i < 5; $i++) {
    		$secret .= $possible[rand(0, strlen($possible)-1)];
    	}
    	
    	return $secret;
	}
	
	/*
	 * Bewegt Paddel eine Einheit nach Oben
	 */
	public static function moveUp($Key, $SecureKey) {
	  return player::move($Key, $SecureKey, 1);
	}
	
	/*
	 * Bewegt Paddel eine Einheit nach Unten
	 */
	public static function moveDown($Key, $SecureKey) {
	  return player::move($Key, $SecureKey, -1);
	}
	
	/*
	 * bewegt Paddel
	 */
	private static function move($Key, $SecureKey, $distance){
		$game = new game($Key);
		$gameObj = sessionManager::$game;
		$config = sessionManager::$config;
		
		$distance = $distance * $config->PADDLE_STEP;
		
		$returnValue = "OK";
		
		if ($gameObj->players["right"]["SecureKey"] == $SecureKey){
			//wenn rechter Spieler
			
			if ($config->FIELD_HEIGHT < ($gameObj->paddleRight + ($config->PADDLE_HEIGHT /2) + $distance) ||
				($gameObj->paddleRight - ($config->PADDLE_HEIGHT /2) + $distance) < 0){
				//wenn Paddel außerhalb des Spielfeldes
				response::$error = true;
				$returnValue ="leave gamearea";
			}
			elseif ($gameObj->rightMoveCounter > $config->NUMBER_OF_PADDLE_MOVES){
				//wenn zu häufig das Paddel bewegt wurde
				response::$error = true;
				$returnValue ="too many moves";
			} else {
				//Paddel verschieben
				$gameObj->rightMoveCounter = $gameObj->rightMoveCounter + 1;
				$gameObj->paddleRight = $gameObj->paddleRight + $distance;
			}
		} elseif ($gameObj->players["left"]["SecureKey"] == $SecureKey){
			//wenn linker Spieler
			
			if ($config->FIELD_HEIGHT < ($gameObj->paddleLeft + ($config->PADDLE_HEIGHT /2)  + $distance)||
				($gameObj->paddleLeft - ($config->PADDLE_HEIGHT /2) + $distance) < 0){
				//wenn Paddel außerhalb des Spielfeldes
				response::$error = true;
				$returnValue ="leave gamearea";
			}
			elseif ($gameObj->leftMoveCounter > $config->NUMBER_OF_PADDLE_MOVES){
				//wenn Paddel außerhalb des Spielfeldes
				response::$error = true;
				$returnValue ="too many moves";
			} else {
				//Paddel verschieben
				$gameObj->leftMoveCounter  = $gameObj->leftMoveCounter  + 1;
				$gameObj->paddleLeft  = $gameObj->paddleLeft  + $distance;
			}
		} else {
			response::$error = true;
			$returnValue ="not your game";
		}
		
		return $returnValue;
	}
	
}