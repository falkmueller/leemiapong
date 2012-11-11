var Host;
var Name;
var GameKey;
var SecretKey;
var runStatusLoop = false;
var SetKeyTrigger = false;

/*gibt an, in welchen Abstan dea Spielfeld geupdated wird (Milisekunden)*/
var LoopWait = 100;

var Name2 = false;
var SecretKey2 = false;

var PaddelHight;
var ballRadius;

/*Überprüft ob das Spiel gestartet werden kann*/
function checkGameStart(){
	
	Name2 = false;
	SecretKey2 = false;
	
	var hash = window.location.hash;
	runStatusLoop = false;
	
	/*wenn Ankel mit game beginnt, dann*/
	if (hash.search(/#game.+/gi) >= 0){
		
		/*Parameter aus Anker-URL lesen*/
		Host = getParam("host");
		Name = getParam("name");
		Name2 = getParam("name2");
		GameKey = getParam("key");
		
		/*Spiel Starten*/
		if(Host && Name && GameKey) {
			FirstStart();
			return true;
		}
		
		/*Fehlermeldung angeigen, wenn Spiel nicht gestartet werden konnt*/
		ShowMessage("Es ist ein Fehler ausgetreten. Bitte versuchen Sie es erneut.");
		window.location.hash = "#";
		LoadPage('#');
	}
	
}

/*
 * Startkonfiguration
 */
function FirstStart(){
	runStatusLoop = true;
	
	/*Spielfeld an Konfigurationsdaten anpassen*/
	Callconfig();
	
	/*Wenn 2 Spieler imselben Fenster Spielen, dann weiten Spieler einlogggen, sonst Suetr-Knöpfe des Zweiten Spielers ausblenden */
	if (Name2){CallLoginPlayer2();}
	else {
		$('#Player2Legend').hide();
		$('#PlayerLegend').css('float','none');
		$('#PlayerLegend').css('margin','0 auto');
		}
	
	/*Spieler einloggen*/
	CallLogin();
	
	/*Spiel über APi starten*/
	CallStart();
	
	/*Longpolling Statusabfrage starten*/
	StartStatusLoop();

	/*Tastaturtrigger setzen*/
	if(!SetKeyTrigger){
		$(window).keypress(function(event) {return CheckKeyPressEvent(event);});
		SetKeyTrigger = true;
		}
}

/*
 * Tastatureingabe überprüfen und entsprechnede Funktion aufrufen
 */
function CheckKeyPressEvent(evt){
			
	/*KeyCode herausfinden*/
	var keyCode;
	if (!evt) var evt = window.event
	if (evt.which) keyCode = evt.which;
	else if (evt.keyCode) keyCode = evt.keyCode;

	if(keyCode == 38){
		//Key UP
		paddleUp(1);
		return false;
	}
	else if (keyCode == 40){
		//key down
		paddleDown(1);
		return false;
	} 
	else if (keyCode == 119){
		//Letter 'w'
		paddleUp(2);
	}
	else if (keyCode == 115){
		//Letter 's'
		paddleDown(2);
	}
	else if (keyCode == 82){
		//Letter 'r'
		restart();
		return false;
	}
	
	return true;
}

/*
 * Wenn Parameter runStatusLoop = true, dann Endlosschleife durchführen, welche das SPielFeld updates 
 */
function StartStatusLoop(){
	if (runStatusLoop){
		 $.ajax({
			  type: 'POST',
			  url: Host + "/" + GameKey + "/status",
			  error: function(data){setTimeout(StartStatusLoop, LoopWait)},
			  success: function(data){UpdateStatus(data); setTimeout(StartStatusLoop, LoopWait)},
			  timeout: 30000,
			  dataType: "json"
		});
	}
}

/*
 * Aktualisiert das Spielfeld
 */
function UpdateStatus(data){

	/*Namen Setzen*/
	$('#nameLeft').text(data['players']['left']);
	$('#nameRight').text(data['players']['right']);
	
	/*Punkte setzen und herausfinden, ob ein Spieler einen Punkt gemacht hat*/
	var hasMakePoint = false;
	if ( $('#pointsLeft').text() != data['scoreLeft'] ||
			$('#pointsRight').text() != data['scoreRight']){
		hasMakePoint = true;
		try {$("#beep-one")[0].play();} catch (e) {}
	}
	$('#pointsLeft').text(data['scoreLeft']);
	$('#pointsRight').text(data['scoreRight']);
	
	
	/*Ball setzen*/ 
	if (hasMakePoint) {
		/*Wenn ein Punkt gemacht wurde, dann Ball direkt auf Koordinate setzen*/
		$('#ball').css('left', data['ball']['0'] - ballRadius);
		$('#ball').css('bottom', data['ball']['1'] - ballRadius);
	} else {
		/*Ball animiert bewegen, damit dieser weniger ruckelt*/
		$('#ball').animate({left: data['ball']['0'] - ballRadius, bottom: data['ball']['1'] - ballRadius}, LoopWait, 'linear');
	}

	/*Paddel positionieren*/
	$('#paddleLeft').css('bottom', (data['paddleLeft'] - PaddelHight /2));
	$('#paddleRight').css('bottom', (data['paddleRight'] - PaddelHight /2));

	
	/*Wenn Spiel beendet ist, dann Endloaschleife beenden und Anzeigen, wer gewonnen hat*/
	if (data["status"] == "finished"){
		runStatusLoop = false;
		var WinPlayerName = (data['scoreLeft'] > data['scoreRight']) ? data['players']['left'] : data['players']['right'];
		$.blockUI({ message: "Spieler " + WinPlayerName + " hat gewonnen.<br/><div class='bt bt_center'><a onclick='restart()'>Neues Spiel</a></div>"});
	}

}

/*
 * Wenn Spiel beendet ist, kann es über diese Funktion wieder gestartet werden
 */
function restart(){
	runStatusLoop = true;
	CallStart();
	$.unblockUI();
	StartStatusLoop();
}

/*
 * ruft Konfiguration von API ab
 */
function Callconfig(){
	$.ajax({ url: Host + "/" + GameKey + "/config", 
	success: function(data){SetConfig(data);},
	async: false,
	dataType: "json"
    });
}

/*
 * Übergibt an API ein JSON Objekt, welches am Server als Spiel-Konfiguration genutzt werden soll
 */
function SetConfig(configObj){
	$.ajax({ url: Host + "/" + GameKey + "/config", 
	data: {config: configObj},
	success: function(data){SetConfig(data);},
	async: false,
	dataType: "json"
    });
}

/*
 * Loggt Spieler über API ins Spiel ein
 */
function CallLogin(){
	$.ajax({ url: Host + "/" + GameKey + "/player/" + Name,
	success: function(data){SecretKey = data;},
	error: function(a,b,c){ShowMessage(a.responseText);},
	async: false,
	dataType: "json"
    });
}

/*
 * Loggt Spieler 2 ein (bei Spielmodus mit 2 Spielern in einem Browser)
 */
function CallLoginPlayer2(){
	$.ajax({ url: Host + "/" + GameKey + "/player/" + Name2,
	success: function(data){if (data != "Game full"){SecretKey2 = data;}},
	async: false,
	dataType: "json"
    });
}

/*
 * Spiel über API starten
 */
function CallStart(){
	$.ajax({ url: Host + "/" + GameKey + "/start",
	async: false
    });
}

/*
 * Konfiguriert Spielfeld, Paddel und Ball
 */
function SetConfig(data){
	
	$('#Field').width(data['FIELD_WIDTH']);
	$('#Field').height(data['FIELD_HEIGHT']);
	
	$('#ball').width(data['BALL_RADIUS']*2);
	$('#ball').height(data['BALL_RADIUS']*2);
	$('#ball').css('left', (data['FIELD_WIDTH'] / 2 - data['BALL_RADIUS']));
	$('#ball').css('bottom', (data['FIELD_HEIGHT'] / 2 - data['BALL_RADIUS']));
	
	$('#paddleLeft').width(data['PADDLE_WIDTH']);
	$('#paddleLeft').height(data['PADDLE_HEIGHT']);
	$('#paddleLeft').css('bottom', (data['FIELD_HEIGHT'] / 2 - data['PADDLE_HEIGHT'] /2));
	
	$('#paddleRight').width(data['PADDLE_WIDTH']);
	$('#paddleRight').height(data['PADDLE_HEIGHT']);
	$('#paddleRight').css('bottom', (data['FIELD_HEIGHT'] / 2 - data['PADDLE_HEIGHT'] /2));
	
	PaddelHight = data['PADDLE_HEIGHT'];
	ballRadius = data['BALL_RADIUS'];
}

/*
 * Paddel eine Einheit nach oben bewegen
 */
function paddleUp(Player){
	if (Player == 2){$.ajax({ url: Host + "/" + GameKey + "/player/" + Name2 + "/" + SecretKey2 + "/up"});}
	else {$.ajax({ url: Host + "/" + GameKey + "/player/" + Name + "/" + SecretKey + "/up"});}
	
}

/*
 * Paddel eine Einheit nach unten Bewegen
 */
function paddleDown(Player){
	if (Player == 2){$.ajax({ url: Host + "/" + GameKey + "/player/" + Name2 + "/" + SecretKey2 + "/down"});}
	else {$.ajax({ url: Host + "/" + GameKey + "/player/" + Name + "/" + SecretKey + "/down"});}

}

/*
 * Gibt Parameter aus Anker zurück
 */
function getParam(variable){ 
     var query = window.location.hash.split("?")[1];  
     var vars = query.split("&"); 
      for (var i=0;i<vars.length;i++) {   
            var pair = vars[i].split("=");  
            if(pair[0] == variable){return pair[1];}
       }       return(false);
}
 
