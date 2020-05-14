# Internetschach Changelog

## Version 0.5.0 (2020-05-14)

* Fix: Spielerliste.php findet bei Leerzeichen nach Komma keinen Namen
* tl_internetschach_tabellen Grundstruktur vervollständigt
* tl_internetschach_preise erstellt und ausgebaut (group_callback funktioniert noch nicht richtig)
* Spielerliste.php: Einschränkung der Ausgabe auf höchste zulässige DWZ

## Version 0.4.0 (2020-05-13)

* Add: tl_internetschach_tabellen (Grundstruktur)
* Fix: Zeitstempel wurde auch bei leerem Bemerkungsfeld hinzugefügt
* Add: Antwortadresse (reply-to) bei Anmelde-Email eingefügt
* Add: Felder für Termin/Meldeschluß der Turniere in tl_internetschach
* Add: Anmeldeformular berücksichtigt Meldeschlüsse der Turniere
* Fix: Anmeldeformular schreibt Gruppe als serialisiertes Array in die DB, statt als String

## Version 0.3.0 (2020-05-12)

* Fix: Export der Anmeldungen an das Bundle angepaßt
* Add: Erkennung von Mehrfachanmeldungen
* Fix: %s wurde nicht ersetzt in den Sprachdateien
* Fix: Infoseite Anmeldungen korrigiert

## Version 0.2.0 (2020-05-11)

* Add: Turniere/Gruppen in Anmeldelisten optional ausgeben

## Version 0.1.0 (2020-05-09)

* Add: Helper-Klasse erstellt
* Add: Helper-Funktion Gruppenzuordnung
* Add: Anzeige von Gruppe + Gruppenberechtigung in Ausgabe Spielerliste.php
* Add: Spalte gruppe in tl_internetschach_anmeldungen
* Fix: Nach Name sortierte Ausgabe von Spielerliste.php
* Fix: Farbliche Hervorhebung der ungeprüften Anmeldungen im Frontend durch CSS ersetzt
* Fix: Backend-Ansicht tl_spieler_anmeldungen
* Add: Registrierungsdatum in tl_spieler_anmeldungen

## Version 0.0.6 (2020-05-08)

* Reload der Seite nach Formularversand

## Version 0.0.5 (2020-05-08)

* Fix: Fehler bei Ausgabe Anmeldeliste

## Version 0.0.4 (2020-05-08)

* Ausbau zur Betaversion

## Version 0.0.3 (2020-05-07)

* Entfernung gnat/simple-php-form

## Version 0.0.2 (2020-05-07)

* Ausbau der Funktionen

## Version 0.0.1 (2020-05-05)

* Initialversion
