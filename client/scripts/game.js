var Host;
var Name;
var GameKey;
var SecretKey;
var runStatusLoop = false;
var LoopWait = 100;

var PaddelHight;
var ballRadius;

function checkGameStart(){
	var hash = window.location.hash;
	runStatusLoop = false;
	
	if (hash.search(/#game.+/gi) >= 0){
	
		Host = getParam("host");
		Name = getParam("name");
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
	CallLogin();
	//starten
	CallStart();
	//Longpolling Starten
	StartStatusLoop();

	//Tastaturtrigger setzen

	$(window).keypress(function(event) {return CheckKeyPressEvent(event);});

}

function CheckKeyPressEvent(event){

	if(event.keyCode == 38){
		paddleUp();
		return false;
	}
	else if (event.keyCode == 40){
		paddleDown();
		return false;
	}
	else if (event.keyCode == 83){
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
	}
	$('#pointsLeft').text(data['scoreLeft']);
	$('#pointsRight').text(data['scoreRight']);
	
	//ball setzen 
	if (hasMakePoint) {
		$('#ball').css('left', data['ball']['0'] - ballRadius);
		$('#ball').css('bottom', data['ball']['1'] - ballRadius);
	} else {
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

function paddleUp(){
	$.ajax({ url: Host + "/" + GameKey + "/player/" + Name + "/" + SecretKey + "/up",
    });
}

function paddleDown(){
	$.ajax({ url: Host + "/" + GameKey + "/player/" + Name + "/" + SecretKey + "/down",
    });
}

function getParam(variable){ 
     var query = window.location.hash.split("?")[1];  
     var vars = query.split("&"); 
      for (var i=0;i<vars.length;i++) {   
            var pair = vars[i].split("=");  
            if(pair[0] == variable){return pair[1];}
       }       return(false);
}