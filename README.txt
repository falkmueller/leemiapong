AUTOR:
Falk M�ller
falk@leemia.de

SYSTEMVORRAUSSETZUNG:
Webserver mit PHP unterst�tzung (min. Version 5)

INSTALLATION:
Keine Installation erforderlich,
da keine Datenbank vorliegt und keine Konfoguration get�tigt werden muss.
F�r die KI (Spiel gegen Computer) sollte ein CronJob regelm��ig (0,1 sek) 
die url /robot aufrufen. Sollte kein CronJob m�glichsein, kann man auch 
im Browser die Seite /robot/run.htm aufrufen, welche per AJAX regelm��ig
die CronJob-Seite aufruft. 