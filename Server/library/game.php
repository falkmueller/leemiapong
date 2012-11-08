<?php 

class game {
	
	private static $game;
	private static $key;

	public function __construct($key){
		if (!game::$game){
			game::$key = $key;
			game::$game = $this->GetGame($key);	
		}
	}
	
	public function config(){
		return new configObj();
	}
	
	public function status(){
		$this->run();
		return game::$game->Get();
	}
	
	public function start(){
		if (game::$game->status == statusEnum::$STATUS_FINISHED or game::$game->status == statusEnum::$STATUS_READY){
			game::$game->scoreLeft = 0;
			game::$game->scoreRight = 0;
			game::$game->status = statusEnum::$STATUS_READY;
			game::$game->lastBallMove = round(microtime(true)*1000);
			$this->resetBall();
		}
		return "OK";
	}
	
	public function resetBall(){
		game::$game->status = statusEnum::$STATUS_READY;
		game::$game->lastBallMove = round(microtime(true)*1000);
		
		$config = new configObj();
		game::$game->ball = array($config->FIELD_WIDTH / 2, $config->FIELD_HEIGHT /2);
  		game::$game->ballDelta = array($this->random($config->INITIAL_BALL_SPEED), $this->random($config->INITIAL_BALL_SPEED));
	}
	
	private function random($value){
		$direction = (rand(0,100)/100) < 0.5 ? -1 : 1;
		return $direction * ((rand(0,100)/100) * $value / 2 + $value / 2);
	}
	
	private function GetGame($key){
		
		session_id($key);
		session_start();
		
		if(!isset($_SESSION["game"])){
			$GameObj = new gameObj();
			$_SESSION["game"] = $GameObj;
		} 
		
		return $_SESSION["game"];
		
	}
	
	public function GameObj() {
		return game::$game;
	}
	
	private function Save(){
		session_write_close();
		session_start();
	}
	
	private function run(){
		$config = new configObj();
		
		//Wenn wartezeit vorbei ($lastBallMove < now + $WAIT_BEFORE_START), dann status = $STATUS_STARTED
		if (game::$game->status == statusEnum::$STATUS_READY){
			if (game::$game->lastBallMove < time() - $config->WAIT_BEFORE_START){
			game::$game->status = statusEnum::$STATUS_STARTED;
			}
		}
		
		//wenn eine sekunge vergangen, dann Move counter zurücksetzen und lastmoveCounter reset auf aktuelles datum setzen
		if (game::$game->LastMoveCounterReset < round(microtime(true)*1000) - $config->WAIT_BEFORE_NUMBER_OF_PADDLE_MOVES_RESET){
			game::$game->LastMoveCounterReset = round(microtime(true)*1000);
			game::$game->leftMoveCounter = 0;
			game::$game->rightMoveCounter = 0;
		}
		
		//wenn status = $STATUS_STARTED, dann stept errechnen (vergangene zeit seit letzten ballmove / $TIME_QUANTUM) und durchführen
		if (game::$game->status == statusEnum::$STATUS_STARTED){
			$Steps = round((round(microtime(true)*1000) - game::$game->lastBallMove) / $config->TIME_QUANTUM);
			game::$game->lastBallMove = round(microtime(true)*1000);
			
			for ($i = 1; $i <= $Steps; $i++) {
				$this->Step($config);
			}
			
			//$this->Save();
		}
	}
	
	private function Step($config){
		//Ball bewegen
		
		//1. wenn ball ganz rechts ist
		if(game::$game->ball[0] >= $config->FIELD_WIDTH - $config->BALL_RADIUS - $config->PADDLE_WIDTH){
			if(game::$game->ball[1] > game::$game->paddleRight - $config->PADDLE_HEIGHT/2 and 
				game::$game->ball[1] < game::$game->paddleRight + $config->PADDLE_HEIGHT/2){
				//wenn ball an Paddel
				game::$game->ballDelta[0] = game::$game->ballDelta[0]*(-1);
				game::$game->ballDelta[1] = game::$game->ballDelta[1] + ((game::$game->ball[1] - game::$game->paddleRight)/ $config->ACCELORATOR);
			
				//Ball beschleunigen
				game::$game->ballDelta[0] = game::$game->ballDelta[0] * (1 + $config->ACCELORATE_PER_ROUND);
				game::$game->ballDelta[1] = game::$game->ballDelta[1] * (1 + $config->ACCELORATE_PER_ROUND);
				}	
			else {
				//wenn ball nicht anpaddel
				$this->resetBall();
				game::$game->scoreLeft++;
				CheckWinner($config);
				return;
			}
		}
		
		//2. wenn ball ganz links ist
		if(game::$game->ball[0] <= $config->BALL_RADIUS + $config->PADDLE_WIDTH){
			if(game::$game->ball[1] > game::$game->paddleLeft - $config->PADDLE_HEIGHT/2 and 
				game::$game->ball[1] < game::$game->paddleLeft + $config->PADDLE_HEIGHT/2){
				//wenn ball an Paddel
				game::$game->ballDelta[0] = game::$game->ballDelta[0]*(-1);
				game::$game->ballDelta[1] = game::$game->ballDelta[1] + ((game::$game->ball[1] - game::$game->paddleLeft)/ $config->ACCELORATOR);
			
				//Ball beschleunigen
				game::$game->ballDelta[0] = game::$game->ballDelta[0] * (1 + $config->ACCELORATE_PER_ROUND);
				game::$game->ballDelta[1] = game::$game->ballDelta[1] * (1 + $config->ACCELORATE_PER_ROUND);
				}	
			else {
				//wenn ball nicht anpaddel
				$this->resetBall();
				game::$game->scoreRight++;
				CheckWinner($config);
				return;
			}
		}
		
		//3. wenn ball an den oberen oder unteren Spielfeldrand ist
		if (game::$game->ball[1] >= $config->FIELD_HEIGHT - $config->BALL_RADIUS or 
			game::$game->ball[1] <= $$config->BALL_RADIUS){
			game::$game->ballDelta[1] = game::$game->ballDelta[1] * (-1);
		}
		
		//4. Ball Position verändern
		game::$game->ball[0] = game::$game->ball[0] + game::$game->ballDelta[0];
		game::$game->ball[1] = game::$game->ball[1] +game::$game->ballDelta[1];
	}
	
	private function CheckWinner($config){
		if (game::$game->scoreRight >= $config->SCORE_TO_WIN or 
			game::$game->scoreLeft >= $config->SCORE_TO_WIN){
			game::$game->status = statusEnum::$STATUS_FINISHED;
			}
	}
}