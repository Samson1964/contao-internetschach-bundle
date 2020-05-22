<?php

namespace Schachbulle\ContaoInternetschachBundle\Classes;

if (!defined('TL_ROOT')) die('You cannot access this file directly!');


/**
 * Class dsb_trainerlizenzExport
  */
class Qualifikationen extends \Backend
{

	public function Aktualisieren()
	{
		if($this->Input->get('key') != 'qualifikationen')
		{
			return '';
		}

		$aktZeit = time();
		
		// Turnierserie einlesen
		$objSerie = \Database::getInstance()->prepare('SELECT * FROM tl_internetschach WHERE id = ?')
		                                    ->execute(\Input::get('id'));

		// Tabellen einlesen
		$objTabellen = \Database::getInstance()->prepare("SELECT * FROM tl_internetschach_tabellen WHERE pid = ?")
		                                       ->execute($objSerie->id);

		// Turniere/Gruppen der Serie in Arrays laden
		$turniere = unserialize($objSerie->turniere);
		$gruppen = unserialize($objSerie->gruppen);

		// Benutzernamen der Turniere der jeweiligen Gruppe initialisieren
		// Hier werden die Qualifikationen gespeichert
		$Benutzer = array();
		foreach($gruppen as $gruppe)
		{
			$Benutzer[$gruppe['feldname']] = array();
		}

		// Definierte Turniere der Reihe nach prüfen
		foreach($turniere as $turnier)
		{
			// Definierte Gruppen der Reihe nach prüfen, wenn das Turnier den Spieltermin erreicht hat
			if($aktZeit > $turnier['termin'])
			{
				// Turnier wahrscheinlich schon beendet
				foreach($gruppen as $gruppe)
				{
					//echo "Prüfe Turnier ".$turnier['feldname']." Gruppe ".$gruppe['feldname']."<br>";
					// Tabelle suchen, vorher Objekt zurücksetzen
					$objTabellen->reset();
					if($objTabellen->numRows)
					{
						while($objTabellen->next())
						{
							//echo "... Tabelle aus Turnier ".$objTabellen->turnier." Gruppe ".$objTabellen->gruppe."<br>";
							if($objTabellen->turnier == $turnier['feldname'] && $objTabellen->gruppe == $gruppe['feldname'] && $objTabellen->importArray)
							{
								//echo "... ... Übereinstimmung! Tabelle wird aktualisiert<br>";
								$tabelleArr = unserialize($objTabellen->importArray); // Tabelle in Array umwandeln
								// Disqualifizierte Spielernummern laden (Spielernummer = Platz + 1)
								$disqualifiziert = \Schachbulle\ContaoHelperBundle\Classes\Helper::StringToArray($objTabellen->disqualifikation);
								// Turnier/Gruppe gefunden und Tabelle wurde bereits importiert
								// Turnier jetzt auswerten
								$finale = 0; // Bisher 0 Spieler qualifiziert
								//echo "<pre>";
								//print_r($tabelleArr);
								//echo "</pre>";
								for($platz = 0; $platz < count($tabelleArr); $platz++)
								{
									// Qualifikation zurücksetzen
									$tabelleArr[$platz]['qualification'] = '';
									if($platz == 0) continue; // Kopfzeile überspringen

									// Nur nichtdisqualifizierte Spieler berücksichtigen
									if(!in_array($platz + 1, $disqualifiziert))
									{
										// Spieler nur berücksichtigen, wenn es noch Finalplätze gibt
										if($finale < $gruppe['qualifikationen'])
										{
											if($Benutzer[$gruppe['feldname']][$tabelleArr[$platz]['cb-name']])
											{
												// Spieler ist schon qualifiziert, Gruppe übernehmen
												$tabelleArr[$platz]['qualification'] = $Benutzer[$gruppe['feldname']][$tabelleArr[$platz]['cb-name']];
											}
											else
											{
												// Spieler ist noch nicht qualifiziert, Gruppe übernehmen
												$tabelleArr[$platz]['qualification'] = $turnier['feldname'];
												$Benutzer[$gruppe['feldname']][$tabelleArr[$platz]['cb-name']] = $turnier['feldname'];
												$finale++;
											}
										}
									}
								}
								// Tabelle aktualisieren
								$set = array
								(
									'importArray' => serialize($tabelleArr)
								);
								$objDB = \Database::getInstance()->prepare("UPDATE tl_internetschach_tabellen %s WHERE id = ?")
								                                 ->set($set)
								                                 ->execute($objTabellen->id);
							}
						}
					}
				}
			}
		}

		// Zurück zur Seite
		\Controller::redirect(str_replace('&key=qualifikationen', '', \Environment::get('request')));
	}

}
