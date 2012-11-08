function LoadPage(hash){
	$("#Content").slideUp("fast", function(){
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

function ShowContent(cont){
	$("#Content").html(cont);
	$("#Content").slideDown("slow", function(){SetInacticeCusor();checkGameStart();});
 
}

function SetActiceCusor(){
	document.body.style.cursor = "wait";
}
function SetInacticeCusor(){
	document.body.style.cursor = "default";
}


function WindowLoadEvent(){
	LoadPage(window.location.hash);
	$('a[href^="#"]').click( function(){SetActiceCusor(); 
												LoadPage($(this).attr("href"));
												})
}

function startGame(){

	if (ValidInput()){
		window.location.hash = "game?host=" + $("#GameHost").val() + "&key=" + $("#GameKey").val() + "&name=" + $("#GameName").val();
		LoadPage("#game?host=" + $("#GameHost").val() + "&key=" + $("#GameKey").val() + "&name=" + $("#GameName").val());
	}
}

var isSearchRival = false;

function SearchRival(btnCtrl){
	if (ValidInput()){
		if (isSearchRival){
			$("#GameHost").prop('disabled', false);
			$("#GameKey").prop('disabled', false);
			$("#GameName").prop('disabled', false);
			
			$("#SearchWaitIcon").hide();
			isSearchRival = false;
			$(btnCtrl).html("Gegner suchen")
		} else {	
			$("#GameHost").prop('disabled', true);
			$("#GameKey").prop('disabled', true);
			$("#GameName").prop('disabled', true);
		
			isSearchRival = true;
			$(btnCtrl).html("Abbrechen");
			$("#SearchWaitIcon").show();
			SeachRevalLoop();
		}
	}
}

function SeachRevalLoop(){
	
	$.ajax({ url: "client/webservice.php", 
			type: 'POST',
	    	success: function(data){if(isSearchRival){SeachRevalLoopFound(data);}},
	        data: {key: $("#GameKey").val(), SearchPlayer: 1}, 
	        error:  function(res,textStatus,errorThrown){if(isSearchRival){SeachRevalLoop();}},
	        timeout: 30000 });
}

function SeachRevalLoopFound(Key){
	ShowMessage("Sie werden nun verbunden.");
	$("#GameKey").val(Key);
	startGame();
}

function ValidInput(){
	//Prügen das kein Istgleich in den angaben
	$("#GameHost").val($("#GameHost").val().replace('=','').replace('&','').replace('?','').replace('#',''));
	$("#GameKey").val($("#GameKey").val().replace(/[^a-z0-9]/gi,''));
	$("#GameName").val($("#GameName").val().replace('=','').replace('&','').replace('?','').replace('#',''));

	//Prüfen ob alle angeaben gemacht sind
	if ($("#GameHost").val() == ""){ShowMessage("Bitte geben Sie einen Host an."); return false;}
	if ($("#GameKey").val() == ""){ShowMessage("Bitte geben Sie einen Key an."); return false;}
	if ($("#GameName").val() == ""){ShowMessage("Bitte geben Sie Ihren Namen an."); return false;}
	
	return true;
}

function ShowMessage(mes){
	$.blockUI({ message: mes });
	setTimeout("$.unblockUI()", 1500);
}