<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Pong</title>
<meta name="fragment" content="!">

<link rel="stylesheet" type="text/css" href="client/layout/styles.css" media="all" />

<script type="text/javascript" src="client/scripts/jquery-1.8.2.min.js" ></script>
<script type="text/javascript" src="client/scripts/jquery.blockUI.js" ></script>
<script type="text/javascript" src="client/scripts/start.js" ></script>
<script type="text/javascript" src="client/scripts/game.js" ></script>
</head>
<body onload="WindowLoadEvent();">
	<div class="PageWrapper">
		<h1><a href="#">Pong</a></h1>
		<div class="Headertext">
			Play the ultamate Game
		</div>
		
		<div id="Content">
		<!-- Load per AJAX -->
		<!-- Falls Crawer von Suchmaschine BEGIN-->
		<?php
		if(isset($_GET["_escaped_fragment_"])){
			include 'webservice.php';
			echo GetContentFromHash($_GET["_escaped_fragment_"]);
		}		
		?>
		<!-- Falls Crawer von Suchmaschine END-->
		</div>
		<div id="footer">
			Dieses Pong Spiel entstand im Rahmen des <a target="_blank" href="http://www.coding-contest.de/">Coding Contest 2012</a>
			<br/>
			Weite Informationen, sowie eine Spielanleitung, den Download und die Dokumentation finden Sie  <a href="#info">hier</a>.
			<br/><br/>
			Falk Müller, 2012
			<div id="impress">
				Impressum
				<div id="impress-text">
					Falk Müller, 04347 Leipzig, pong@leemia.de
				</div>
			</div>
		</div>
	</div>
	
	<div style="display: none;">
		<audio id="beep-one" controls preload="auto">
			<source src="client/sounds/BLOOP.mp3" controls></source>
			<source src="client/sounds/BLOOP.ogg" controls></source>
		</audio>
			<audio id="pageLoad" controls preload="auto">
			<source src="client/sounds/CAMERAC.mp3" controls></source>
			<source src="client/sounds/CAMERAC.ogg" controls></source>
		</audio>
	</div>

</body>
</html>