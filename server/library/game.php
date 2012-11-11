<?php 
/*
 * beinhaltet alle Funktionen die mit dem Spiel zu tun haben
 */
class game {
	
	/*
	 * l‰sst den ChronJob laufen und gibt den Status des Spiels zur¸ck
	 */
	public function status(){
		$this->run();
		return sessionManager::$game->Get();
	}
	
	/*
	 * Wenn Spiler eingeloggt sind und Spiel gerade nicht l‰uft, dann wird Spiel (neu) gestartet
	 */
	public function start(){
		$game = sessionManager::$game;
		
		if ($game->status == statusEnum::$STATUS_FINISHED or $game->status == statusEnum::$STATUS_READY){
			$game->scoreLeft = 0;
			$game->scoreRight = 0;
			$game->status = statusEnum::$STATUS_READY;
			$game->lastBallMove = round(microtime(true)*1000);
			$this->resetBall();
		}
		return "OK";
	}
	
	/*
	 * setzt den Ball zur¸ck in die Mitte und gint Ihn eine zuf‰llige Richtung
	 */
	public function resetBall(){
		
		$game = sessionManager::$game;
		$config = sessionManager::$config;
		
		$game->status = statusEnum::$STATUS_READY;
		$game->lastBallMove = round(microtime(true)*1000);
		
		$game->ball = array($config->FIELD_WIDTH / 2, $config->FIELD_HEIGHT /2);
  		$game->ballDelta = array($this->random($config->INITIAL_BALL_SPEED), $this->random($config->INITIAL_BALL_SPEED));
	}
	
	/*
	 * Erzeugt eine Zufallszahl Zwischen $value und $value/2 mit zuf‰lligen Vorzeichen
	 */
	private function random($value){
		$direction = (rand(0,100)/100) < 0.5 ? -1 : 1;
		return $direction * ((rand(0,100)/100) * $value / 2 + $value / 2);
	}
	
	/*
	 * Cronjob, welcher regelm‰ﬂig aufgerifen wird und das Spielgeschehen vorrantreibt
	 */
	private function run(){
		$game = sessionManager::$game;
		$config = sessionManager::$config;
		
		//Wenn wartezeit vorbei ($lastBallMove < now + $WAIT_BEFORE_START), dann status = $STATUS_STARTED
		if ($game->status == statusEnum::$STATUS_READY){
			if ($game->lastBallMove < round(microtime(true)*1000) - $config->WAIT_BEFORE_START){
			$game->status = statusEnum::$STATUS_STARTED;
			$game->lastBallMove = round(microtime(true)*1000);
			}
		}
		
		//wenn eine Sekunge vergangen, dann PaddelMoveCounter zur¸cksetzen und lastmoveCounter auf aktuelle Zeit (ms) setzen
		if ($game->LastMoveCounterReset < round(microtime(true)*1000) - $config->WAIT_BEFORE_NUMBER_OF_PADDLE_MOVES_RESET){
			$game->LastMoveCounterReset = round(microtime(true)*1000);
			$game->leftMoveCounter = 0;
			$game->rightMoveCounter = 0;
		}
		
		//wenn status = $STATUS_STARTED, dann stept errechnen (vergangene zeit seit letzten ballmove / $TIME_QUANTUM) und durchf¸hren
		if ($game->status == statusEnum::$STATUS_STARTED){
			$Steps = round((round(microtime(true)*1000) - $game->lastBallMove) / $config->TIME_QUANTUM);
			$game->lastBallMove = round(microtime(true)*1000);
			
			//Schrittweiﬂe Ball verschieben
			for ($i = 1; $i <= $Steps; $i++) {
				$this->Step();
			}
			
		}
	}
	
	/*
	 * Einzelner Schritt (Ballbewegung)
	 */
	private function Step(){
		$game = sessionManager::$game;
		$config = sessionManager::$config;
		
		//viertel Breite des Balls berechnen (wird bei der ‹berpr¸fung benˆtigt, ob Ball auf Paddel trifft)
		$ballQuarter = ($config->BALL_RADIUS /2);
		
		//1. wenn Ball ganz rechts ist
		if($game->ball[0] >= $config->FIELD_WIDTH - $config->BALL_RADIUS - $config->PADDLE_WIDTH){
			
			if($game->ball[1] + $ballQuarter  > $game->paddleRight - $config->PADDLE_HEIGHT/2 and 
				$game->ball[1] - $ballQuarter < $game->paddleRight + $config->PADDLE_HEIGHT/2){
				//wenn Ball an Paddel
				$game->ballDelta[0] = $game->ballDelta[0]*(-1);
				$game->ballDelta[1] = $game->ballDelta[1] + (($game->ball[1] - $game->paddleRight)/ $config->ACCELORATOR);			
				}	
			else {
				//wenn Ball nicht anpaddel
				$this->resetBall();
				$game->scoreLeft++;
				$this->CheckWinner();
				return;
			}
		}
		
		//2. wenn Ball ganz links ist
		if($game->ball[0] <= $config->BALL_RADIUS + $config->PADDLE_WIDTH){
			
			if($game->ball[1] + $ballQuarter > $game->paddleLeft - $config->PADDLE_HEIGHT/2 and 
				$game->ball[1] - $ballQuarter < $game->paddleLeft + $config->PADDLE_HEIGHT/2){
				//wenn Ball an Paddel
				$game->ballDelta[0] = $game->ballDelta[0]*(-1);
				$game->ballDelta[1] = $game->ballDelta[1] + (($game->ball[1] - $game->paddleLeft)/ $config->ACCELORATOR);
				}	
			else {
				//wenn Ball nicht anpaddel
				$this->resetBall();
				$game->scoreRight++;
				$this->CheckWinner();
				return;
			}
		}
		
		//3. wenn Ball an den oberen oder unteren Spielfeldrand ist
		if ($game->ball[1] >= $config->FIELD_HEIGHT - $config->BALL_RADIUS or 
			$game->ball[1] <= $config->BALL_RADIUS){
			//vertikale Richtung wechseln
			$game->ballDelta[1] = $game->ballDelta[1] * (-1);
		}
		
		//4.Ball beschleunigen
		$game->ballDelta[0] = $game->ballDelta[0] * (1 + $config->ACCELORATE_PER_ROUND);
		$game->ballDelta[1] = $game->ballDelta[1] * (1 + $config->ACCELORATE_PER_ROUND);
	
		//5.Ball Position ver‰ndern
		$game->ball[0] = $game->ball[0] + $game->ballDelta[0];
		$game->ball[1] = $game->ball[1] + $game->ballDelta[1];
	}
	
	/*
	 * ‹berpr¸ft ein ein Spieler gewonnen hat und ‰ndert ggf. den Spielstatus
	 */
	private function CheckWinner(){
		$game = sessionManager::$game;
		$config = sessionManager::$config;
		
		if ($game->scoreRight >= $config->SCORE_TO_WIN or 
			$game->scoreLeft >= $config->SCORE_TO_WIN){
			$game->status = statusEnum::$STATUS_FINISHED;
			}
	}
}