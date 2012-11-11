<?php 
/**
 * Klasse erzeugt Antwort für Client
 */
class response {

	private $url;
	private $responseObj;
	public static $error = false;
	
	public  function __construct($url){
		$this->url = $url;
		$this->run();
	}
	
	/*
	 * Erkennt anhand der URL, welche API Funktion Ausgerufen werden soll
	 */
	private function run(){
		
		if (preg_match("#(.*)/status#", $this->url, $treffer)){
			//Status liefern
			$key = substr($treffer[1], 1);
			new sessionManager($key);
			$game = new game();
			
			//Erweiterung ROBOT
			if (!is_null(sessionManager::$robot)){new robot();}
			
			$this->responseObj = $game->status();
		} elseif (preg_match("#(.*)/config#", $this->url, $treffer)){
			//Konfiguration liefern
			$key = substr($treffer[1], 1);
			new sessionManager($key);
			
			if(isset($_POST["config"])){
				//wenn KonfigurationsObjekt übermittelt wurde, dann dieses verwenden
				sessionManager::AlterConfig($_POST["config"]);
			}

			$this->responseObj = sessionManager::$config;
		} elseif (preg_match("#(.*)/start#", $this->url, $treffer)){
			//Siel starten
			$key = substr($treffer[1], 1);
			new sessionManager($key);
			$game = new game();
			$this->responseObj = $game->start();
		} elseif (preg_match("#(.*)/player/(.*)/(.*)/up#", $this->url, $treffer)){
			//Paddel nach oben bewegen
			$key = substr($treffer[1], 1);
			$Playername = $treffer[2];
			$SecureKey = $treffer[3];
						
			new sessionManager($key);
			$this->responseObj = player::moveUp($key, $SecureKey);
		} elseif (preg_match("#(.*)/player/(.*)/(.*)/down#", $this->url, $treffer)){
			//Paddel nach unten bewegen
			$key = substr($treffer[1], 1);
			$Playername = $treffer[2];
			$SecureKey = $treffer[3];
			
			new sessionManager($key);
			$this->responseObj = player::moveDown($key, $SecureKey);
		}  elseif (preg_match("#(.*)/player/(.*)#", $this->url, $treffer)){
			//Spieler dem Spiel hinzufügen
			$key = substr($treffer[1], 1);
			$Playername = $treffer[2];
						
			new sessionManager($key);
			$this->responseObj = player::login($key, $Playername);
		}  elseif (preg_match("#(.*)/robot#", $this->url, $treffer)){
			//Robot dem Spiel hinzufügen
			$key = substr($treffer[1], 1);
						
			new sessionManager($key);
			new robot();
		}
		
	}

	/*
	 * Gibt das Rückbageobjekt als JSON String zurück
	 */
	public function send(){
		
		if (response::$error){header("HTTP/1.0 400 Bad Request");}
		
		echo json_encode($this->responseObj);
	}
}