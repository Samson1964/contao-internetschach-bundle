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

		// Definierte Turniere der Reihe nach prüfen
		foreach($turniere as $turnier)
		{
			// Definierte Gruppen der Reihe nach prüfen, wenn das Turnier den Spieltermin erreicht hat
			if($aktZeit > $turnier['termin'])
			{
				// Turnier wahrscheinlich schon beendet
				foreach($gruppen as $gruppe)
				{
					echo "Prüfe Turnier ".$turnier['feldname']." Gruppe ".$gruppe['feldname']."<br>";
					// Tabelle suchen, vorher Objekt zurücksetzen
					$objTabellen->reset();
					if($objTabellen->numRows)
					{
						while($objTabellen->next())
						{
							echo "... Tabelle aus Turnier ".$objTabellen->turnier." Gruppe ".$objTabellen->gruppe."<br>";
							if($objTabellen->turnier == $turnier['feldname'] && $objTabellen->gruppe == $gruppe['feldname'] && $objTabellen->importArray)
							{
								echo "... ... Übereinstimmung! Tabelle wird aktualisiert<br>";
								$tabelleArr = unserialize($objTabellen->importArray); // Tabelle in Array umwandeln
								// Turnier/Gruppe gefunden und Tabelle wurde bereits importiert
								//'ungewertet'       => $objTemp->ungewertet,
								//'disqualifikation' => $objTemp->disqualifikation,
								//'importArray'      => unserialize($objTemp->importArray)
							}
							else
							{
								echo "... ... Keine Übereinstimmung! Tabelle wird nicht aktualisiert<br>";
							}
						}
					}
		//echo "<pre>";
		//print_r($turnier['feldname']);
		//print_r($gruppe['feldname']);
		//echo "</pre>";
				}
			}
		}

		// Zurück zur Seite
		//\Controller::redirect(str_replace('&key=qualifikationen', '', \Environment::get('request')));
	}

}
