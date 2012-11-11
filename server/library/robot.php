<?php 

/*
 * kleine KI für das Spiel gegen den Computer
 */
class robot {
	
	private $RobotName = "Computer";
	
	/*
	 * Erstellt Robot, falls keiner Vorhanden und Steuert Paddel des Robots
	 */
	public function __construct(){
		
		if (is_null(sessionManager::$robot)){$this->createRobot();}
		
		if (!is_null(sessionManager::$robot)){
			$this->run();
		}
		
		response::$error = false;
	}
	
	/*
	 * Meldet Computer als Gegenspieler an und erstellt RobotObj in Session
	 */
	private function createRobot(){
		$secret = player::login(sessionManager::$key, $this->RobotName, $side);
		
		if($secret){
		 	$robotObj = new robotObj();
		 	$robotObj->secret = $secret;
		 	$robotObj->side = $side;
		 	$_SESSION["robot"] = $robotObj;
		}
		
	}
	
	/*
	 * Logic, nach welcher das Paddel gesteuert wird
	 */
	private function run(){

		$Status = $this->GetStatus();
		$config = sessionManager::$config;
		$robot = sessionManager::$robot;
		
		$direktionToMe = false;
		if ($robot->side == "left" and $Status->ballDelta[0] < 0){$direktionToMe = true;}
		if ($robot->side == "right" and $Status->ballDelta[0] > 0){$direktionToMe = true;}
		
		if ($robot->side == "left"){$paddel = $Status->paddleLeft;}
		else {$paddel = $Status->paddleRight;}
		
		if ($direktionToMe and $Status->status == statusEnum::$STATUS_STARTED){
			if ($robot->side == "left"){
				
				$DeltaH = ($Status->ball[0] /  abs($Status->ballDelta[0])) * $Status->ballDelta[1];
				
				$DeltaH =  $DeltaH + $Status->ball[1];
				
				$DeltaH2 = $this->modulo($DeltaH , $config->FIELD_HEIGHT);
				
				if ($this->Div($DeltaH, $config->FIELD_HEIGHT) % 2 == 0){
					if ($DeltaH2 < 0) {$DeltaH2 = $config->FIELD_HEIGHT + $DeltaH2;}
					$DeltaH = $DeltaH2;
				} else {
					$DeltaH = $this->modulo($config->FIELD_HEIGHT - $DeltaH2,$config->FIELD_HEIGHT) ;
				}
				
				$robot->nextH = $DeltaH;
		
			} else {
				$DeltaH = (($config->FIELD_WIDTH - $Status->ball[0]) /  abs($Status->ballDelta[0])) * $Status->ballDelta[1];
				
				$DeltaH =  $DeltaH + $Status->ball[1];
				
				$DeltaH2 = $this->modulo($DeltaH , $config->FIELD_HEIGHT);
				
				if ($this->Div($DeltaH, $config->FIELD_HEIGHT) % 2 == 0){
					if ($DeltaH2 < 0) {$DeltaH2 = $config->FIELD_HEIGHT + $DeltaH2;}
					$DeltaH = $DeltaH2;
				} else {
					$DeltaH =  $this->modulo($config->FIELD_HEIGHT - $DeltaH2, $config->FIELD_HEIGHT);
				}
				
				$robot->nextH = $DeltaH;
			}
		} else {
			$robot->nextH = $config->FIELD_HEIGHT /2;
		}
		
				if ($robot->nextH > $paddel + $config->BALL_RADIUS/4){
					player::moveUp(sessionManager::$key, $robot->secret);
				}
				if  ($robot->nextH < $paddel - $config->BALL_RADIUS/4){
					player::moveDown(sessionManager::$key, $robot->secret);
				}
	}
	
private function modulo ( $x, $y ) {
	$q = $x/$y;
	$q = (int) $q;
	$r = $x - $q*$y;
	return $r;
}

private function Div ($x, $y){
	$q = $x/$y;
	$q = floor( $q );
	return $q; 
}
	
	/*
	 * Holt den Status des Spiels
	 */
	private function GetStatus(){
		return sessionManager::$game->Get();
	}
	
}