<?php 
	$Host = WSURL();
	$Key= CreateKey();

?>
<div class="start">
	<div class="left"> 
		<h2>Spiel Starten</h2>
		<p>
			Game-Host:<br/>
			<input type="text" value="<?php echo $Host; ?>" id="GameHost" />
		</p>
		<p>
			Game-Key:<br/>
			<input type="text" value="<?php echo $Key; ?>" id="GameKey" />
		</p>
		<p>
			Dein Name:<br/>
			<input type="text" id="GameName" />
		</p>
		<p>
		<div class="bt">
			<a onclick="startGame();">Spiel starten</a>
		</div>
		<div class="bt">
			<a onclick="SearchRival(this);">Gegner suchen</a>
		</div>
		<div id="SearchWaitIcon">
			<img src='Client/layout/images/waitbutton.gif' />
		</div>
		<div style="clear: both;"></div>
		</p>
	</div>
	<div class="right"> 
		<p>
			Du willst mal wieder klassisches Pong spielen? Dann bist du hier genau richtig.<br/>
			trage einfach deinen Namen ein und dr&uuml;cke "Genger suchen".<br/>
		</p>
		<p>
			Wenn du mit einem Freund Spielen willst, dann trage den Game-Kay deines Kumpels ein und dr&uuml;cke "Spiel starten".<br/>
			Du kannst auch selbst ein Spiel starten und einen Freund deinen Game-Key mitteilen.
		</p>
	</div>
	<div style="clear: both;"></div>
</div>