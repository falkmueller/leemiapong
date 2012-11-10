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
		<p id="pKey">
			Game-Key:<br/>
			<input type="text" value="<?php echo $Key; ?>" id="GameKey" />
		</p>
		<p>
			Dein Name:<br/>
			<input type="text" id="GameName" />
		</p>
		<p id="pPlayer2">
			Name Spieler Zwei:
			<input type="text" id="GameName2" />
		</p>
		<p>
			<div class="bt">
				<a onclick="startGame();">Spiel starten</a>
			</div>
		</p>
	</div>
	<div class="right"> 
		<p>
			Du willst mal wieder klassisches Pong spielen? Dann bist du hier genau richtig.<br/>
			trage einfach deinen Namen ein und dr&uuml;cke "Genger suchen".<br/>
			<div class="bt" style="float: left;">
				<a onclick="SearchRival(this);">Gegner suchen</a>
			</div>
			<div id="SearchWaitIcon">
				<img src='client/layout/images/waitbutton.gif' />
			</div>
			<div style="clear: both;"></div>
		</p>
		<p>
			Wenn du mit einem Freund Spielen willst, dann trage den Game-Kay deines Kumpels ein und dr&uuml;cke "Spiel starten". 
			Du kannst auch selbst ein Spiel starten und einen Freund deinen Game-Key mitteilen.<br/>
		</p>
		<p>
			Wenn Ihr zu zweit in einem Fenster Spielen wollt, dann dr&uuml;cke den folgenden Button.
			<div class="bt">
				<a onclick="ShowPlayer2Input();">2 Spieler</a>
			</div>
		</p>
	</div>
	<div style="clear: both;"></div>
</div>