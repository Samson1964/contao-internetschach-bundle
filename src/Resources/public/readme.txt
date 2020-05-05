Landesverband: 00000 - Deutscher Schachbund

DWZ-Datenbank vom 29.04.2020 - 92228 Spieler in 2354 Vereinen

===========================================================================
Eine Veröffentlichung dieser Daten ist nur nach vorheriger Abspache mit dem
zuständigen DWZ-Referenten erlaubt!
===========================================================================

Dateistruktur: (ANSI-Dateien, die Felder sind durch "," getrennt.)

spieler.csv - sortiert nach DWZ
-----------
- ZPS-Nummer des Vereins
- Mitgliedsnummer im Verein
- Status der Mitgliedschaft
    A - Aktiv
    P - Passiv
- Name,Vorname
- Geschlecht
    M - Männlich
    W - Weiblich
- Spielberechtigung
    D - Deutscher
    G - Gleichgestellt
    E - EU-Ausländer
    A - Ausländer
    S - Sperre
- Geburtsjahr
- Woche der letzten Turnierauswertung (JJJJWW)
- DWZ
- Index
- FIDE-Elozahl
- FIDE-Titel
    CM - Candidate Master          WCM - Woman Candidate Master
    FM - FIDE-Master               WFM - Woman FIDE-Master
    IM - International Master      WIM - Woman International Master
    GM - Grandmaster               WGM - Woman Grandmaster
- FIDE-ID
- FIDE-Land

vereine.csv - sortiert nach ZPS-Nummer
-----------
- ZPS-Nummer des Vereins
- Landesverband
- Übergeordneter Verband
- Vereinsname

verband.csv - sortiert nach Verbandnummer
-----------
- Verbandnummer
- Landesverband
- Übergeordneter Verband
- Verbandname
