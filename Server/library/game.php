<?php 

class game {
	
	private static $game;
	private static $key;

	public function __construct($key){
		if (!game::$game){
			game::$key = $key;
			game::$game = $this->GetGame($key);	
			$this->run();
		}
	}
	
	public function config(){
		return new configObj();
	}
	
	public function status(){
		return game::$game->Get();
	}
	
	public function start(){
		if (game::$game->status == statusEnum::$SSTATUS_FINISHED or game::$game->status == statusEnum::$STATUS_READY){
			game::$game->status = statusEnum::$STATUS_READY;
			$this->resetBall();
			$this->Save();
		}
		return "OK";
	}
	
	public function resetBall(){
		$config = new configObj();
		game::$game->ball = array($config->FIELD_WIDTH / 2, $config->FIELD_HEIGHT /2);
  		game::$game->ballDelta = array($this->random($config->INITIAL_BALL_SPEED), $this->random($config->INITIAL_BALL_SPEED));
	}
	
	private function random($value){
		$direction = (rand(0,100)/100) < 0.5 ? -1 : 1;
		return $direction * ((rand(0,100)/100) * $value / 2 + $value / 2);
	}
	
	private function GetGame($key){
		if (file_exists("games/".$key)) {
			$FileContent = 	file_get_contents("games/".$key);
			return unserialize($FileContent);
		} else {
			$GameObj = new gameObj();
			$FileContent = serialize($GameObj);
			$fh = fopen("games/".$key, 'w') or die("can't open file");
			fwrite($fh, $FileContent);
			fclose($fh);
			return $GameObj;
		}
	}
	
	public function GameObj() {
		return game::$game;
	}
	
	public function Save(){
		$FileContent = serialize(game::$game);
		$fh = fopen("games/".game::$key, 'w') or die("can't open file");
		fwrite($fh, $FileContent);
		fclose($fh);
	}
	
	private function run(){
		//Wenn wartezeit vorbei ($lastBallMove < now + $WAIT_BEFORE_START), dann status = $STATUS_STARTED
		if (game::$game->status == statusEnum::$STATUS_READY){
			//TODO
		}
		
		//wenn eine sekunge vergangen, dann Move counter zurücksetzen und lastmoveCounter reset auf aktuelles datum setzen
			//TODO
		
		//wenn status = $STATUS_STARTED, dann stept errechnen (vergangene zeit seit letzten ballmove / $TIME_QUANTUM) und durchführen
			//TODO
	}
	
	private function Step(){
		//Ball bewegen
		//gewinn checken
	}
}