<?php

namespace Schachbulle\ContaoInternetschachBundle\Classes;

if (!defined('TL_ROOT')) die('You cannot access this file directly!');


/**
 * Class dsb_trainerlizenzExport
  */
class Preise extends \Backend
{

	public function Aktualisieren()
	{
		if($this->Input->get('key') != 'preise')
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
		// Hier werden die Preise gespeichert
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
					// Tabelle suchen, vorher Objekt zurücksetzen
					$objTabellen->reset();
					if($objTabellen->numRows)
					{
						while($objTabellen->next())
						{
							//echo "... Tabelle aus Turnier ".$objTabellen->turnier." Gruppe ".$objTabellen->gruppe."<br>";
							if($objTabellen->turnier == $turnier['feldname'] && $objTabellen->gruppe == $gruppe['feldname'] && $objTabellen->importArray)
							{
								$tabelleArr = unserialize($objTabellen->importArray); // Tabelle in Array umwandeln
								// Disqualifizierte Spielernummern laden (Spielernummer = Platz + 1)
								$disqualifiziert = \Schachbulle\ContaoHelperBundle\Classes\Helper::StringToArray($objTabellen->disqualifikation);
								$platz = 1;
								$platz_dwz = 1;
								// Turnier jetzt auswerten
								for($zeile = 0; $zeile < count($tabelleArr); $zeile++)
								{
									// Qualifikation zurücksetzen
									$tabelleArr[$zeile]['prices'] = array();
									if($zeile == 0) continue; // Kopfzeile überspringen

									// Nur nichtdisqualifizierte Spieler berücksichtigen
									if(!in_array($zeile + 1, $disqualifiziert))
									{
										// Anmeldedaten des Spielers laden
										$objAnmeldung = \Schachbulle\ContaoInternetschachBundle\Classes\Helper::getAnmeldung($objSerie->id, $tabelleArr[$zeile]['cb-name']);
										//print_r($objAnmeldung['dwz']);
										// Preis suchen (Hauptpreise, ohne DWZ-Grenze)
										$objPreis = \Database::getInstance()->prepare("SELECT * FROM tl_internetschach_preise WHERE pid = ? AND turnier = ? AND gruppe = ? AND platz = ? AND dwz_grenze = ? AND published = ?")
										                                    ->execute($objSerie->id, $objTabellen->turnier, $objTabellen->gruppe, $platz, 0, 1);
										if($objPreis->numRows)
										{
											//echo "$zeile: Platz $platz, Turnier ".$objTabellen->turnier.", Gruppe ".$objTabellen->gruppe.", Preis: ".$objPreis->name."<br>";
											$tabelleArr[$zeile]['prices'][] = $objPreis->id;
											$platz++;
										}
										// Preis suchen (DWZ-Preise)
										$objPreis = \Database::getInstance()->prepare("SELECT * FROM tl_internetschach_preise WHERE pid = ? AND turnier = ? AND gruppe = ? AND platz = ? AND dwz_grenze = ? AND published = ?")
										                                    ->execute($objSerie->id, $objTabellen->turnier, $objTabellen->gruppe, $platz_dwz, $gruppe['dwz_kategoriegrenze'], 1);
											//echo $gruppe['dwz_kategoriegrenze']." - ";
											//echo $objAnmeldung['dwz']." - ";
											//echo $objPreis->id."<br>";
										if($objPreis->numRows)
										{
											if($objAnmeldung['dwz'] < $objPreis->dwz_grenze)
											{
												//echo "$zeile: Platz $platz_dwz, Turnier ".$objTabellen->turnier.", Gruppe ".$objTabellen->gruppe.", Preis: ".$objPreis->name."<br>";
												$tabelleArr[$zeile]['prices'][] = $objPreis->id;
												$platz_dwz++;
											}
										}
									}
								}
								//echo "<pre>";
								//print_r($tabelleArr);
								//echo "</pre>";
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
		//\Controller::redirect(str_replace('&key=preise', '', \Environment::get('request')));
	}

}
