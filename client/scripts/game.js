var Host;
var Name;
var GameKey;
var SecretKey;
var runStatusLoop = false;
var LoopWait = 100;

var Name2 = false;
var SecretKey2 = false;

var PaddelHight;
var ballRadius;

function checkGameStart(){
	
	Name2 = false;
	SecretKey2 = false;
	
	var hash = window.location.hash;
	runStatusLoop = false;
	
	if (hash.search(/#game.+/gi) >= 0){
	
		Host = getParam("host");
		Name = getParam("name");
		Name2 = getParam("name2");
		GameKey = getParam("key");
		
		if(Host && Name && GameKey) {
			FirstStart();
			return true;
		}
		
		ShowMessage("Es ist ein Fehler ausgetreten. Bitte versuchen Sie es erneut.");
		window.location.hash = "#";
		LoadPage('#');
	}
	
}

function FirstStart(){
	runStatusLoop = true;
	
	//Spielfeld an Konfigurationsdaten anpassen
	Callconfig();
	//login
	if (Name2){CallLoginPlayer2();}
	else {
		$('#Player2Legend').hide();
		$('#PlayerLegend').css('float','none');
		$('#PlayerLegend').css('margin','0 auto');
		}
	
	CallLogin();
	//starten
	CallStart();
	//Longpolling Starten
	StartStatusLoop();

	//Tastaturtrigger setzen

	$(window).keypress(function(event) {return CheckKeyPressEvent(event);});

}

function CheckKeyPressEvent(evt){
	
		var keyCode;
		if (!evt) var evt = window.event
		if (evt.which) keyCode = evt.which;
		else if (evt.keyCode) keyCode = evt.keyCode;
	
		console.log(evt);

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
		// Letter 'r'
		restart();
		return false;
	}
	
	return true;
}

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

function UpdateStatus(data){

	//Namen Setzen
	$('#nameLeft').text(data['players']['left']);
	$('#nameRight').text(data['players']['right']);
	
	//Punkte setzen
	var hasMakePoint = false;
	if ( $('#pointsLeft').text() != data['scoreLeft'] ||
			$('#pointsRight').text() != data['scoreRight']){
		hasMakePoint = true;
		try {$("#beep-one")[0].play();} catch (e) {}
	}
	$('#pointsLeft').text(data['scoreLeft']);
	$('#pointsRight').text(data['scoreRight']);
	
	//ball setzen 
	if (hasMakePoint) {
		$('#ball').css('left', data['ball']['0'] - ballRadius);
		$('#ball').css('bottom', data['ball']['1'] - ballRadius);
	} else {
		//ball bewegen
		$('#ball').animate({left: data['ball']['0'] - ballRadius, bottom: data['ball']['1'] - ballRadius}, LoopWait);	
	}
	
	//Paddel setzen
	$('#paddleLeft').css('bottom', (data['paddleLeft'] - PaddelHight /2));
	$('#paddleRight').css('bottom', (data['paddleRight'] - PaddelHight /2));


	if (data["status"] == "finished"){
		runStatusLoop = false;
		var WinPlayerName = (data['scoreLeft'] > data['scoreRight']) ? data['players']['left'] : data['players']['right'];
		$.blockUI({ message: "Spieler " + WinPlayerName + " hat gewonnen.<br/><a onclick='restart()'>Neues Spiel</a>"});
	}

}

function restart(){
	runStatusLoop = true;
	CallStart();
	$.unblockUI();
	StartStatusLoop();
}

function Callconfig(){
	$.ajax({ url: Host + "/" + GameKey + "/config", 
	success: function(data){SetConfig(data);},
	async: false,
	dataType: "json"
    });
}

function CallLogin(){
	$.ajax({ url: Host + "/" + GameKey + "/player/" + Name,
	success: function(data){if (data != "Game full"){SecretKey = data;}},
	async: false,
	dataType: "json"
    });
}

function CallLoginPlayer2(){
	$.ajax({ url: Host + "/" + GameKey + "/player/" + Name2,
	success: function(data){if (data != "Game full"){SecretKey2 = data;}},
	async: false,
	dataType: "json"
    });
}

function CallStart(){
	$.ajax({ url: Host + "/" + GameKey + "/start",
	async: false
    });
}

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

function paddleUp(Player){
	if (Player == 2){$.ajax({ url: Host + "/" + GameKey + "/player/" + Name2 + "/" + SecretKey2 + "/up"});}
	else {$.ajax({ url: Host + "/" + GameKey + "/player/" + Name + "/" + SecretKey + "/up"});}
	
}

function paddleDown(Player){
	if (Player == 2){$.ajax({ url: Host + "/" + GameKey + "/player/" + Name2 + "/" + SecretKey2 + "/down"});}
	else {$.ajax({ url: Host + "/" + GameKey + "/player/" + Name + "/" + SecretKey + "/down"});}

}

function getParam(variable){ 
     var query = window.location.hash.split("?")[1];  
     var vars = query.split("&"); 
      for (var i=0;i<vars.length;i++) {   
            var pair = vars[i].split("=");  
            if(pair[0] == variable){return pair[1];}
       }       return(false);
}
 
