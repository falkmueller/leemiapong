/*Parameter gibt an, ob gerade eine Schleife l�uft um einen Gegner zu finden*/
var isSearchRival = false;

/*
 * Sorg daf�r, dass Anhang eines URL-Strings der dazugeh�rige Inhalt geladen wird
 */
function LoadPage(hash){
	
	/*Melodie Abspielen und Mauszeiger auf Warten stellen*/
	SetActiceCusor();
	try {$("#pageLoad")[0].play();} catch (e) {}
	
	/*Hauptinhalt ausblenden*/
	$("#Content").slideUp("fast", function(){
		/*Inhalt f�r Hauptfenser anhand von �bergebenen URL-String laden und an Funktion ShowContent �bergeben*/
		 $.ajax({
			  type: 'POST',
			  url: "client/webservice.php",
			  data: {hash: hash},
			  dataType: "html",
			  success: function(res){ShowContent(res)},
			  error:  function(res,textStatus,errorThrown){ if (window.location.hostname == 'localhost') { alert(textStatus+": "+errorThrown); alert(res);}}
		});
	});
	
	 
}

/*
 * Blendet den �bergeben Inhalt ein
 */
function ShowContent(cont){
	
	/*Inhalt in ausgeblendeten Container laden*/
	$("#Content").html(cont);
	
	/*Container einblenden*/
	$("#Content").slideDown("slow", 
							function(){
								/*Mauszeiger auf Normal stellen*/
								SetInacticeCusor();
								/*Im neu geladen Inhalt allen AnkerLinks eine Clickfunktion setzen, damit beim Klicken auf die Links per AJAX der Inhalt geladen wird*/
								$('#Content a[href^="#"]').click( function(){LoadPage($(this).attr("href"));})
								/*Funktion aufrufen, welche Pr�ft, ob das Spiel beginnen kann*/				
								checkGameStart();
							}); 
}

/*
 * Setz den Maiszeiger auf "Warten"
 */
function SetActiceCusor(){
	document.body.style.cursor = "wait";
}

/*
 * Setz den Maiszeiger auf "Normal"
 */
function SetInacticeCusor(){
	document.body.style.cursor = "default";
}

/*
 * Initialisierungsfunktion nach Pageload
 */
function WindowLoadEvent(){
	/*allen AnkerLinks eine Clickfunktion setzen, damit beim Klicken auf die Links per AJAX der Inhalt geladen wird*/
	$('a[href^="#"]').click( function(){LoadPage($(this).attr("href"));});
	/*Per Ajax Hauptinhalt zum Aktull gesetzten Anker nachladen*/
	LoadPage(window.location.hash);
}

/*
 * Leitet zum Spielfeld weiter
 */
function startGame(){
	
	/*Wenn Angaben valide sind*/
	if (ValidInput()){
		
		/*Wenn Spielmodus "2 Spieler" ist, dann in Anker den Namen des Spielers mitgeben*/
		var Player2Name = "";
		if(!$("#pPlayer2").is(':hidden')){
			Player2Name = "&name2=" + $("#GameName2").val();
		}
		
		/*Anker bilden, mit allen Parametern, welche das Spiel ben�tigt*/
		window.location.hash = "game?host=" + $("#GameHost").val() + "&key=" + $("#GameKey").val() + "&name=" + $("#GameName").val() + Player2Name;
		/*Speilfeld laden*/
		LoadPage("#game?host=" + $("#GameHost").val() + "&key=" + $("#GameKey").val() + "&name=" + $("#GameName").val() + Player2Name);
	}
	
}


/*
 * Funktion, welche einen Spielgegner sucht
 */
function SearchRival(btnCtrl){
	
	/*Nur wenn Eingaben Valide sind*/
	if (ValidInput()){
		
		/*Wenn bereits nach Gegner gesucht wird*/
		if (isSearchRival){
			/*Eingefelder wieder �nderbar machen*/
			$("#GameHost").prop('disabled', false);
			$("#pKey").show();
			$("#GameName").prop('disabled', false);
			
			$("#SearchWaitIcon").hide();
			isSearchRival = false;
			$(btnCtrl).html("Gegner suchen")
		} else {	
			/*Eingabefelder w�rend gegner gesucht wird sperren*/
			$("#GameHost").prop('disabled', true);
			$("#GameName").prop('disabled', true);
			/*Feld des Keys wird ausgeblendet, da Key bei Suche vom Server kommt*/
			$("#pKey").hide();
			
			/*Anzeige und Button anpassen*/
			isSearchRival = true;
			$(btnCtrl).html("Abbrechen");
			$("#SearchWaitIcon").show();
			
			/*Schleife starten, welche Gegner sucht*/
			SeachRevalLoop();
		}
	}
}

/*
 * Funktion, welche per long polling solange zum Server eine Verbindung h�lt, bis ein Gegner gefunden ist
 */
function SeachRevalLoop(){
	
	$.ajax({ url: "client/webservice.php", 
			type: 'POST',
	    	success: function(data){if(isSearchRival){SeachRevalLoopFound(data);}},
	        data: {key: $("#GameKey").val(), SearchPlayer: 1}, 
	        error:  function(res,textStatus,errorThrown){if(isSearchRival){SeachRevalLoop();}},
	        timeout: 30000 });
}

/*
 * Wenn ein Gegner gefunden ist, dann Nachricht anzeigen und zum Spiel leiten
 */
function SeachRevalLoopFound(Key){
	ShowMessage("Sie werden nun verbunden.");
	$("#GameKey").val(Key);
	startGame();
}

/*
 * Pr�ft ob Formulareingaben valide sind
 */
function ValidInput(){
	//Pr�gen das kein Istgleich oder & in den Angaben ist
	$("#GameHost").val($("#GameHost").val().replace('=','').replace('&','').replace('?','').replace('#',''));
	$("#GameName").val($("#GameName").val().replace('=','').replace('&','').replace('?','').replace('#',''));
	$("#GameName2").val($("#GameName2").val().replace('=','').replace('&','').replace('?','').replace('#',''));
	//Beim Key alles  au�er Zahlen und Buchstaben entfernen (da dieser als PHP-SessionId verwendet werden k�nnen soll)
	$("#GameKey").val($("#GameKey").val().replace(/[^a-z0-9]/gi,''));
	
	
	//Pr�fen ob alle Angeaben gemacht sind
	if ($("#GameHost").val() == ""){ShowMessage("Bitte geben Sie einen Host an."); return false;}
	if ($("#GameKey").val() == ""){ShowMessage("Bitte geben Sie einen Key an."); return false;}
	if ($("#GameName").val() == ""){ShowMessage("Bitte geben Sie Ihren Namen an."); return false;}
	
	//Wenn 2 Spiler im selben Browser spielen wollen, dann auch pr�fen ob f�r Spieler 2 ein name angegeben ist
	if(!$("#pPlayer2").is(':hidden')){
		if ($("#GameName2").val() == ""){ShowMessage("Bitte geben einen Namen f�r Spieler 2 an."); return false;}
	}
	
	return true;
}

/*
 * Anzeige/ausblende desEingabefelder f�r den zweiten Spielers
 */
function ShowPlayer2Input(){
	if($("#pPlayer2").is(':hidden')){
		$("#pPlayer2").show();
	}
	else {
		$("#pPlayer2").hide();
	}
	
}

/*
 * Leitet zum Spiel weiter, wenn man gegen den Computer spielen will
 */
function StartGameAgainstRobot(){
	if (ValidInput()){
		 $.ajax({url:  $("#GameHost").val() + "/" + $("#GameKey").val() + "/robot"});
		startGame();
	}
}

/*
 * �bergebene Nachricht f�r 1,5 Sekunden einblenden
 */
function ShowMessage(mes){
	$.blockUI({ message: mes });
	setTimeout("$.unblockUI()", 1500);
}