<?php 
/*
 *Spiel  Konfigurations Objekt 
 */
class configObj {

	//FielfeldHöhe (px)
	public $FIELD_HEIGHT = 500;
	
	//SpielfeldBreite (px)
	public $FIELD_WIDTH = 800;
	
	//Radius des Balls (px)
	public $BALL_RADIUS = 20;
	
	//Höhe der Paddel (px)
	public $PADDLE_HEIGHT = 80;
	
  	//Breite der Paddel (px)
	public $PADDLE_WIDTH = 10;
	
	//Anzahl der Einheiten (px), die ein Paddel sich mit einem Schritt bewegt
  	public $PADDLE_STEP = 20;
  	
  	//Erhöhung der Ballgeschwingigkeit, wenn Paddel nicht mittig getroffen
  	public $ACCELORATOR = 10;
  	
  	//Ballgeschwindigkeit wird pro Runde erhöht mit Hilfe dieses Wertes
  	public $ACCELORATE_PER_ROUND = 0.0001;

  	//Zeitliche Länge eines Schrittes (milisek)
  	public $TIME_QUANTUM = 10;
  	
  	//Ballgeschwindigkeit beim Start
  	public $INITIAL_BALL_SPEED = 2;
  	
  	//Wartezeit vor Spielbeginn (milisek)
  	public $WAIT_BEFORE_START = 1000;
  	
  	//Zeit in der das Paddel max $NUMBER_OF_PADDLE_MOVES mal bewegt werden darf(milisek)
  	public $WAIT_BEFORE_NUMBER_OF_PADDLE_MOVES_RESET = 1000;
	
  	//Maximale Anpahl der Paddelbewegungen in der Zeit WAIT_BEFORE_NUMBER_OF_PADDLE_MOVES_RESET
  	public $NUMBER_OF_PADDLE_MOVES = 10;
	
  	//Wieviel Punkte man zum Soieg erreichen muss
  	public $SCORE_TO_WIN = 10;

}