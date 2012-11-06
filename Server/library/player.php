<?php 

class player {

	public static function login($Key, $Playername) {
		$game = new game($Key);
		$gameObj = $game->GameObj();
		
		if ($gameObj->players["right"]["SecureKey"] != ""){
			return "Game full";
		} 
		
		$Secret = player::CreateSecret();
		
		if($gameObj->players["left"]["SecureKey"] == ""){
			$gameObj->players["left"]["name"] = $Playername;
			$gameObj->players["left"]["SecureKey"] = $Secret;
		} else {
			$gameObj->players["right"]["name"] = $Playername;
			$gameObj->players["right"]["SecureKey"] = $Secret;
			$gameObj->status = statusEnum::$STATUS_READY;
			$game->resetBall();
		}
		
		$game->Save();
		
		return $Secret;
	}
	
	private static function CreateSecret(){
		$secret = "";
    	$possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    	
    	for ($i = 0; $i < 5; $i++) {
    		$secret .= $possible[rand(0, strlen($possible))];
    	}
    	
    	return $secret;
	}
	
	public static function moveUp($Key, $SecureKey) {
		$config = new configObj();
	  return player::move($Key, $SecureKey, $config->PADDLE_STEP);
	}
	
	public static function moveDown($Key, $SecureKey) {
		$config = new configObj();
	  return player::move($Key, $SecureKey, -$config->PADDLE_STEP);
	}
	
	private static function move($Key, $SecureKey, $distance){
		$game = new game($Key);
		$gameObj = $game->GameObj();
		$config = new configObj();
		$returnValue = "OK";
		
		if ($gameObj->players["right"]["SecureKey"] == $SecureKey){
			if ($config->FIELD_HEIGHT < ($gameObj->paddleRight + $distance)){
				$returnValue ="leave gamearea";
			}
			elseif ($gameObj->rightMoveCounter > $config->NUMBER_OF_PADDLE_MOVES){
				$returnValue ="too many moves";
			} else {
				$gameObj->rightMoveCounter = $gameObj->rightMoveCounter + 1;
				$gameObj->paddleRight = $gameObj->paddleRight + $distance;
			}
		} elseif ($gameObj->players["left"]["SecureKey"] == $SecureKey){
			if ($config->FIELD_HEIGHT < ($gameObj->paddleLeft  + $distance)){
				$returnValue ="leave gamearea";
			}
			elseif ($gameObj->leftMoveCounter > $config->NUMBER_OF_PADDLE_MOVES){
				$returnValue ="too many moves";
			} else {
				$gameObj->leftMoveCounter  = $gameObj->leftMoveCounter  + 1;
				$gameObj->paddleLeft  = $gameObj->paddleLeft  + $distance;
			}
		} else {
			$returnValue ="not your game";
		}
		
		$game->Save();
		
		return $returnValue;
	}
	
}