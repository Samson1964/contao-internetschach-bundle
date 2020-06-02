<?php

namespace Schachbulle\ContaoInternetschachBundle\Classes;

if (!defined('TL_ROOT')) die('You cannot access this file directly!');


/**
 * Class Qualifikationen
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
										$chessbaseName = strtolower($tabelleArr[$platz]['cb-name']); // Benutzername in Kleinschreibung
										// Spieler nur berücksichtigen, wenn es noch Finalplätze gibt
										if($finale < $gruppe['qualifikationen'])
										{
											if($Benutzer[$gruppe['feldname']][$chessbaseName])
											{
												// Spieler ist schon qualifiziert, Gruppe übernehmen
												$tabelleArr[$platz]['qualification'] = $Benutzer[$gruppe['feldname']][$chessbaseName];
											}
											else
											{
												// Spieler ist noch nicht qualifiziert, Gruppe übernehmen
												$tabelleArr[$platz]['qualification'] = $turnier['feldname'];
												$Benutzer[$gruppe['feldname']][$chessbaseName] = $turnier['feldname'];
												$finale++;
											}
										}
										else
										{
											// Es gibt keine Plätze mehr im aktuellen Turnier, aber die Altdaten müssen übertragen werden
											if($Benutzer[$gruppe['feldname']][$chessbaseName])
											{
												// Spieler ist schon qualifiziert, Gruppe übernehmen
												$tabelleArr[$platz]['qualification'] = $Benutzer[$gruppe['feldname']][$chessbaseName];
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

	/* Funktion FinaleAktualisieren
	 * Aktualisiert in den Anmeldungen die Chekbox für die Teilnahme am Finale
	 */
	public function FinaleAktualisieren()
	{
		if($this->Input->get('key') != 'finalqualifikationen')
		{
			return '';
		}

		// Turnierserie einlesen
		$objSerie = \Database::getInstance()->prepare('SELECT * FROM tl_internetschach WHERE id = ?')
		                                    ->execute(\Input::get('id'));

		// Tabellen einlesen
		$objTabellen = \Database::getInstance()->prepare("SELECT * FROM tl_internetschach_tabellen WHERE pid = ?")
		                                       ->execute($objSerie->id);

		// Turniere der Serie in Array laden und Finalturnier suchen
		$turniere = unserialize($objSerie->turniere);
		$finale = '';
		foreach($turniere as $turnier)
		{
			if($turnier['finale'])
			{
				$finale = $turnier['feldname'];
				break;
			}
		}
		if(!$finale) return '';

		// Tabellen einlesen, qualifizierte Spieler ermitteln und in Array $benutzer übertragen
		$benutzer = array();
		if($objTabellen->numRows)
		{
			while($objTabellen->next())
			{
				$tabelle = unserialize($objTabellen->importArray); // Tabelle in Array umwandeln
				//echo "<pre>";
				//print_r($tabelle);
				//echo "</pre>";
				// Tabelle auslesen und Qualifikation sichern
				for($i = 1; $i < count($tabelle); $i++)
				{
					if($tabelle[$i]['qualification'])
					{
						$benutzer[] = strtolower($tabelle[$i]['cb-name']);
					}
				}
			}
		}
		$benutzer = array_unique($benutzer);

		// Anmeldungen einlesen
		$objAnmeldungen = \Database::getInstance()->prepare("SELECT * FROM tl_internetschach_anmeldungen WHERE pid = ? AND published = ?")
		                                          ->execute($objSerie->id, 1);
		// Anmeldungen aktualisieren
		if($objAnmeldungen->numRows)
		{
			while($objAnmeldungen->next())
			{
				$turniere = (array)unserialize($objAnmeldungen->turniere);
				//echo "<pre>";
				//print_r($turniere);
				//echo "</pre>";
				// ChessBase-Benutzernamen auflösen
				$benutzernamen = \Schachbulle\ContaoInternetschachBundle\Classes\Helper::getBenutzernamen($objAnmeldungen->chessbase);
				$alterFinalstatus = in_array($finale, $turniere); // Gespeichert: TRUE = Finale, FALSE = nicht qualifiziert
				$neuerFinalstatus = false;
				foreach($benutzernamen as $benutzername)
				{
					$neuerFinalstatus = in_array(strtolower($benutzername), $benutzer); // Neu: TRUE = Finale, FALSE = nicht qualifiziert
					if($neuerFinalstatus) break;
				}
				if($alterFinalstatus != $neuerFinalstatus)
				{
					// Finalstatus muß aktualisiert werden
					if($neuerFinalstatus)
					{
						$turniere[] = $finale;
						$turniere = array_unique($turniere);
					}
					else
					{
						foreach($turniere as $key => $value)
						{
							if($value == $finale) unset($turniere[$key]);
						}
					}
					// Versionierung aktivieren
					$objVersion = new \Versions('tl_internetschach_anmeldungen', $objAnmeldungen->id);
					$objVersion->setUsername('Internetschach-Bundle');
					$objVersion->setUserId(0);
					$objVersion->initialize();
					// set-Array setzen
					$set = array
					(
						'tstamp'       => time(),
						'turniere'     => serialize($turniere)
					);
					//echo "<pre>mod.:";
					//print_r($set['turniere']);
					//echo "</pre>";
					// Datensatz updaten
					$objRecord = \Database::getInstance()->prepare('UPDATE tl_internetschach_anmeldungen %s WHERE id = ?')
					                                     ->set($set)
					                                     ->execute($objAnmeldungen->id);
					$objVersion->create();
					\System::log('A new version of record "tl_internetschach_anmeldungen.id='.$objAnmeldungen->id.'" has been created'.$this->getParentEntries('tl_internetschach_anmeldungen', $objAnmeldungen->id), __METHOD__, TL_GENERAL);
					\System::log('[Internetschach] Geänderte Anmeldung: '.$objAnmeldungen->name, __CLASS__.'::'.__FUNCTION__, TL_CRON);
				}
			}
		}

		//echo "<pre>";
		//print_r($benutzer);
		//echo "</pre>";
		// Zurück zur Seite
		\Controller::redirect(str_replace('&key=finalqualifikationen', '', \Environment::get('request')));
	}
}
