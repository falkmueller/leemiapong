<?php 

class response {

	private $url;
	private $responseObj;
	
	public  function __construct($url){
		$this->url = $url;
		$this->run();
	}
	
	private function run(){
		
		if (preg_match("#(.*)/status#", $this->url, $treffer)){
			//status liefern
			$key = substr($treffer[1], 1);
			$game = new game($key);
			$this->responseObj = $game->status();
		} elseif (preg_match("#(.*)/config#", $this->url, $treffer)){
			//konfiguration liefern
			$key = substr($treffer[1], 1);
			$game = new game($key);
			$this->responseObj = $game->config();
		} elseif (preg_match("#(.*)/start#", $this->url, $treffer)){
			//ist Spiel gestartet
			$key = substr($treffer[1], 1);
			$game = new game($key);
			$this->responseObj = $game->start();
		} elseif (preg_match("#(.*)/player/(.*)/(.*)/up#", $this->url, $treffer)){
			//Paddel nach oben bewegen
			$key = substr($treffer[1], 1);
			$Playername = $treffer[2];
			$SecureKey = $treffer[3];
			$this->responseObj = player::moveUp($key, $SecureKey);
		} elseif (preg_match("#(.*)/player/(.*)/(.*)/down#", $this->url, $treffer)){
			//Paddel nach unten bewegen
			$key = substr($treffer[1], 1);
			$Playername = $treffer[2];
			$SecureKey = $treffer[3];
			$this->responseObj = player::moveDown($key, $SecureKey);
		}  elseif (preg_match("#(.*)/player/(.*)#", $this->url, $treffer)){
			//Spieler dem Spiel hinzufügen
			$key = substr($treffer[1], 1);
			$Playername = $treffer[2];
			$this->responseObj = player::login($key, $Playername);
		}
		
	}

	public function send(){
		echo json_encode($this->responseObj);
	}
}