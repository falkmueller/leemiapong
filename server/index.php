<?php 
error_reporting(0);

/*
 * Autoloader setzen, damit Anhang des Klassennahmens die Datei geladen wird, welche die Klasse enthält
 */
function autoloader($ClassName){
	$ClassName = str_replace("_", "/", $ClassName);
	
	if(file_exists('library/'.$ClassName.'.php')){
		require_once('library/'.$ClassName.'.php');		
	}
}
spl_autoload_register('autoloader');

/*
 * #############################################################
 * JSON BEGINN
 * JSON ist erst ab PHP 5.3. verfügbar
 * Desshalb wird es hier manuell eingebunden, wenn noch nicht vorhanden
 */

if (!function_exists('json_decode')) {
    function json_decode($content, $assoc=false) {
        require_once 'extensions/json/JSON.php';
        if ($assoc) {
            $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
        }
        else {
            $json = new Services_JSON;
        }
        return $json->decode($content);
    }
}

if (!function_exists('json_encode')) {
    function json_encode($content) {
        require_once 'extensions/json/JSON.php';
        $json = new Services_JSON;
        return $json->encode($content);
    }
}

/*
 * JSON END
 * #############################################################
 */

/*
 * Antwort erzeugen und ausgeben
 */
$response = new response($_GET["q"]);
$response->send();

?>