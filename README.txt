AUTOR:
Falk Müller
falk@leemia.de

SYSTEMVORRAUSSETZUNG:
Webserver mit PHP unterstützung (min. Version 5)

INSTALLATION:
Keine Installation erforderlich,
da keine Datenbank vorliegt und keine Konfoguration getätigt werden muss.
Für die KI (Spiel gegen Computer) sollte ein CronJob regelmäßig (0,1 sek) 
die url /robot aufrufen. Sollte kein CronJob möglichsein, kann man auch 
im Browser die Seite /robot/run.htm aufrufen, welche per AJAX regelmäßig
die CronJob-Seite aufruft. 