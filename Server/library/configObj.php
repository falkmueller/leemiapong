<?php 

class configObj {

	//Fielfeldhöhe
	public $FIELD_HEIGHT = 500;
	//SpielfeldBreite
	public $FIELD_WIDTH = 500;
	
	//Radius des Balls
	public $BALL_RADIUS = 5;
	
	//höhe der Paddel
	public $PADDLE_HEIGHT = 80;
  	//Breite der Paddel
	public $PADDLE_WIDTH = 10;
	//Anzahl der Einheiten, die ein Paddel sich mit einem Schritt bewegt
  	public $PADDLE_STEP = 20;
  	
  	//Erhöhung der Ballgeschwingigkeit, wenn paddel nicht mittig getroffen
  	public $ACCELORATOR = 10;
  	//Ballgeschwindigkeit wird pro Runde erhöht mit Hilde dieses Wertes
  	public $ACCELORATE_PER_ROUND = 0.0001;

  	//Millisekunden pro schritt
  	public $TIME_QUANTUM = 10;
  	
  	//Ballgeschwindigkeit beim Start
  	public $INITIAL_BALL_SPEED = 2;
  	
  	//Wartezeit vor spielbeginn
  	public $WAIT_BEFORE_START = 1000;

  	public $NUMBER_OF_PADDLE_MOVES = 10;

  	public $SCORE_TO_WIN = 10;

}