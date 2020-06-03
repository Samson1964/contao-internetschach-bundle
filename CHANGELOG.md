# Internetschach Changelog

## Version 1.4.1 (2020-06-03)

* Fix: Anzahl der Topspieler im Inhaltselement Topspieler

## Version 1.4.0 (2020-06-03)

* Add: Inhaltselement Topspieler

## Version 1.3.3 (2020-06-02)

* Fix: Benutzernamen mit unterschiedlicher Groß- und Kleinschreibung werden bei Qualifikationszuordnung nicht erkannt
* Add: URL zur Fortschrittstabelle des jeweiligen Turniers in den Einstellungen

## Version 1.3.2 (2020-05-28)

* Fix: Finalteilnehmer aktualisieren hatte Problem mit mehreren Benutzernamen

## Version 1.3.1 (2020-05-26)

* Neue Funktion: Preise in den Tabellen eintragen (Hauptpreise, DWZ-Preise, Mehrfachpreise möglich) -> Alphaversion
* Fix: Finalteilnehmer aktualisieren hatte Problem mit Unterscheidung Groß- und Kleinschreibung

## Version 1.3.0 (2020-05-24)

* Inhaltselement Anmeldungen: Blank-Eintrag bei Auswahl Turnierserie hinzugefügt
* Inhaltselement Tabelle: Blank-Eintrag bei Auswahl Turnier/Gruppe hinzugefügt
* Inhaltselement Tabelle: Auswahl Turnier/Gruppe alphabetisch sortiert
* Neue Funktion: Qualifikationen für das Finale in den Anmeldungen eintragen

## Version 1.2.0 (2020-05-23)

* CSS für Qualifikation nur ausgeben, wenn Spalte qualification vorhanden ist
* Gekürzten Vereinsnamen in Tabellen anzeigen
* Fix: Qualifikationen wurden nicht richtig erstellt
* Add: Für Tabellen optional Standard-CSS aktivieren
* Templates korrigiert
 
## Version 1.1.0 (2020-05-22)

* Klasse Qualifikationen fertiggestellt
* Klasse Helper aufgeräumt
* Tabellen: CSS-Klassen für Spaltenart "qualifiziert" ergänzt

## Version 1.0.0 (2020-05-22)

* Tabellen: CSS-Klassen für Spaltenarten ergänzt
* Tabellen: CSS-Klasse für disqualifizierte und ungewertete Spieler
* Klasse Qualifikationen hinzugefügt (noch nicht fertig!)

## Version 0.8.4 (2020-05-20)

* Frontend-CSS hinzugefügt für Tabellen
* Template für Tabellen hinzugefügt

## Version 0.8.3 (2020-05-20)

* Tabelle.php: CSS-Klasse ce_table hinzugefügt
* Funktion importTable: Extrahierung Landeskennzeichen gefixt
* Fix Import der Farbverteilung in Tabellen
* Fix Umwandlung in UTF-8 für die Ausgabe der Tabelle
* Fix Anmeldeformular: Früher gemeldete Turniere wurden bei Update gelöscht

## Version 0.8.2 (2020-05-17)

* Export.php: Feld Name aufgeteilt in zwei Felder Nachname und Vorname (Wunsch ChessBase)

## Version 0.8.1 (2020-05-17)

* Fix Import.php Zeichensatzproblem beim Verarbeiten des importierten HTML (Bug in paquettg/php-html-parser)
* Tabelle.php: Spalte für FIDE-Titel + Name hinzugefügt
* Tabelle.php: Spalte für Qualifikationen hinzugefügt
* Add Helper-Funktion getQualifikationen begonnen

## Version 0.8.0 (2020-05-17)

* Add Tabelle.php - Klasse zur FE-Ausgabe von Tabellen
* tl_content erweitert um Inhaltselement Tabelle
* Add Helper-Funktion getTurnier

## Version 0.7.0 (2020-05-16)

* Übersetzungen Tabellen/Preise ergänzt
* Anmeldeliste: DWZ 0 durch - ersetzt
* Add: Import von Tabellen
* HTML-Parser integriert für Tabellenimport (paquettg/php-html-parser)
* Add: Benutzername "Internetschach-Bundle" in Versionierung
* Turnierserie Gruppen: DWZ-Kategoriegrenze und Qualifikationsplätze hinzugefügt
* Add Backend-CSS (z.B. Style von Links mit class button)
* Aktualisieren-Funktion für Tabellen angefangen
* Add Helper-Funktion getRealname
* Add Helper-Funktion TabelleToCSV begonnen

## Version 0.6.0 (2020-05-15)

* Add: Spielerliste.php: Caching der Suchanfragen eingebaut
* Formularhinweise hinzugefügt

## Version 0.5.3 (2020-05-15)

* Fix: Anmeldebestätigung wird nicht angezeigt (header-Zeile war noch auskommentiert)

## Version 0.5.2 (2020-05-15)

* Spielerliste.php listet Spieler nicht auf -> $objSerie->turniere statt $objSerie->gruppen verwendet bei DWZ-Grenze

## Version 0.5.1 (2020-05-14)

* Fix: tl_version benötigt userid != NULL

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
