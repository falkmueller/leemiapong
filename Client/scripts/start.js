function LoadPage(hash){
	$("#Content").slideUp("fast", function(){
		 $.ajax({
			  type: 'POST',
			  url: "client/webservice.php",
			  data: {hash: hash},
			  success: function(res){ShowContent(res)},
			  error:  function(res,textStatus,errorThrown){ if (window.location.hostname == 'localhost') { alert(textStatus+": "+errorThrown); alert(res);}}
		});
	});
	
	 
}

function ShowContent(cont){
	$("#Content").html(cont);
	$("#Content").slideDown("slow", function(){SetInacticeCusor();});
 
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

function SearchRival(){
	if (ValidInput()){
		$("#GameHost").prop('disabled', true);
		$("#GameKey").prop('disabled', true);
		$("#GameName").prop('disabled', true);
		
		SeachRevalLoop();
	}
}

function SeachRevalLoop(){
	//TODO: schleife zum finden von gegner
	//bei erfolg SeachRevalLoopFound(Key)
	SeachRevalLoopFound("falk");
}

function SeachRevalLoopFound(Key){
	ShopMessage("Sie werden nun verbunden.");
	$("#GameKey").val(Key);
	startGame();
}

function ValidInput(){
	//Prügen das kein Istgleich in den angaben
	$("#GameHost").val($("#GameHost").val().replace('=',''));
	$("#GameKey").val($("#GameKey").val().replace('=',''));
	$("#GameName").val($("#GameName").val().replace('=',''));

	//Prüfen ob alle angeaben gemacht sind
	if ($("#GameHost").val() == ""){ShopMessage("Bitte geben Sie einen Host an."); return false;}
	if ($("#GameKey").val() == ""){ShopMessage("Bitte geben Sie einen Key an."); return false;}
	if ($("#GameName").val() == ""){ShopMessage("Bitte geben Sie Ihren Namen an."); return false;}
	
	return true;
}

function ShopMessage(mes){
	$.blockUI({ message: mes });
	setTimeout("$.unblockUI()", 1000);
}