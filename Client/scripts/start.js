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
