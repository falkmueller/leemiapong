<?php 
/*
 * Verwaltet Session
 */
class sessionManager {
	
	public static $game;
	public static $config;
	public static $robot = NULL;
	public static $key;
	
	/*
	 * Startet Session, holt Spiel und Konfigurationsobjekt
	 */
	public function __construct($key){
			session_id($key);
			session_start();
			
			sessionManager::$key = $key;
			sessionManager::$config = $this->GetConfigFromSession();
			sessionManager::$game = $this->GetGameFromSession();
			sessionManager::$robot = $this->GetRobotFromSession();
	}
	
	/*
	 * schließt die Session, so das Andere Wartende Anfragen Sie verwenden (sperren) können und öffnet Sie wieder (sobald die Session wieder frei ist)
	 */
	private static function Save(){
		session_write_close();
		session_start();
	}
	
	/*
	 * Wenn kein Spiler eingeloggt ist, kann das Konfigurationsobjekt ersetzt werden
	 */
	public static function AlterConfig($config){
		
		if (sessionManager::$game["left"]["SecureKey"] == "" and 
			sessionManager::$game["right"]["SecureKey"] == ""){
				sessionManager::$config = $config;
				sessionManager::$game = new gameObj(sessionManager::$config);
		}
		
	}
	
	/*
	 * holt Spiel aus Session, oder erzeugt neues 
	 */
	private function GetGameFromSession(){
			
		if(!isset($_SESSION["game"])){
			$GameObj = new gameObj(sessionManager::$config);
			$_SESSION["game"] = $GameObj;
		} 
		
		return $_SESSION["game"];
		
	}
	
	/*
	 * Holt Konfiguration aus session oder setzt neues Konfigurationsobjekt
	 */
	private function GetConfigFromSession(){
				
		if(!isset($_SESSION["config"])){
			$configObj = new configObj();
			$_SESSION["config"] = $configObj;
		} 

		return $_SESSION["config"];
	}
	
/*
	 * Holt Robot aus session, wenn vorhanden
	 */
	private function GetRobotFromSession(){
				
		if(isset($_SESSION["robot"])){
				return $_SESSION["robot"];
		} 
		
		return NULL;
	
	}
	
}