<?php 

class gameObj {
	
	//Wann zuletzt der ball bewegt wurde
	public $lastBallMove = null;
	
	//Koordinaten der Ballposition
	public $ball = array(0,0);
	
	//Richtung in der sich der Ball bewegt
	public $ballDelta =array(0,0);
	
	//Höhe, auf der Sich das linkePaddel befindet
	public $paddleLeft = 0;
	
	//Höhe, auf der sich das Rechte Paddel befindet
	public $paddleRight = 0;
	
	//Spieler
	public $players = array("left" => array("name" => "", "SecureKey" => ""), 
							"right" => array("name" => "", "SecureKey" => ""));
	
	//Status des Spiels
	public $status = NULL;
	
	//soll spiel automatisch beginnenm wenn 2 Spieler angemeldet sind
	public $autoStart = true;
	
	//Anzahl der Paddel bewegungen des Linken Spielers
	public $leftMoveCounter = 0; 
	
	//Anzahl der Paddelbewegungen des rechten Spielers
	public $rightMoveCounter = 0;
	
	//Timestamp, wann zuletzt die Paddel Move Counters reresettet wurden
	public $LastMoveCounterReset = NULL;
	
	//Spielstand des linken Spielers
	public $scoreLeft = 0;
	
	//Spielstand des Rechten Spielers
	public $scoreRight = 0;
	
	
	public function __construct(){
		$config = new configObj();
			$this->ball = array($config->FIELD_WIDTH / 2 , $config->FIELD_HEIGHT / 2);
			$this->paddleLeft = $config->FIELD_HEIGHT / 2;
			$this->paddleRight = $config->FIELD_HEIGHT / 2;
			$this->lastBallMove = time();
			$this->LastMoveCounterReset = time();
			$this->status = statusEnum::$STATUS_LOGIN;
	}
	
	public function Get(){
		$returnObj = $this;
		$returnObj->players = array("left" => $this->players["left"]["name"], "right" => $this->players["right"]["name"]);
		
		return $returnObj;
	}
	
	
}