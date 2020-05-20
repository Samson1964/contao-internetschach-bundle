<?php

namespace Schachbulle\ContaoInternetschachBundle\Classes;

class Helper
{

	var $anmeldungenArray = array();

	/**
	 * Funktion Gruppenzuordnung
	 * Liefert den Namen oder den Feldnamen der zugordneten Gruppe zurück
	 * @param $turnierserie     int     ID der Turnierserie
	 * @param $dwz              int     DWZ
	 * @param $feldname         bool    Optional. Statt Gruppenname den Gruppenfeldname liefern bei TRUE
	 * @return string
	 */
	static function Gruppenzuordnung($turnierserie, $dwz, $feldname = false)
	{
		static $gruppen;
		if(!isset($gruppen))
		{
			// Keine Gruppen vorhanden, darum DB abfragen
			$objSerie = \Database::getInstance()->prepare("SELECT * FROM tl_internetschach WHERE id = ?")
			                                    ->execute($turnierserie);
			if($objSerie->numRows)
			{
				$gruppen = unserialize($objSerie->gruppen);
			}
			else $gruppen = array();
		}

		// Gruppenzugehörigkeit prüfen
		foreach($gruppen as $gruppe)
		{
			if($dwz >= $gruppe['dwz_von'] && $dwz <= $gruppe['dwz_bis'])
			{
				// Gruppe gefunden
				if($feldname) $return = $gruppe['feldname'];
				else $return =  $gruppe['name'];
				return $return;
			}
		}
		if($feldname) $return =  '';
		else $return =  'nicht spielberechtigt';
		return $return;
	}

	/**
	 * Funktion ArrayToTurniernamen
	 * Wandelt ein Array mit den Feldnamen der Turniere zu einem Array mit den Turniernamen um 
	 * @param $turnierserie     int     ID der Turnierserie
	 * @param $turniernamen     array   Array mit den Feldnamen der Turniere
	 * @return array                    Array mit den Namen der Turniere
	 */
	static function ArrayToTurniernamen($turnierserie, $turniernamen)
	{
		static $turniere;
		if(!isset($turniere))
		{
			// Keine Turniere vorhanden, darum DB abfragen
			$objSerie = \Database::getInstance()->prepare("SELECT * FROM tl_internetschach WHERE id = ?")
			                                    ->execute($turnierserie);
			if($objSerie->numRows)
			{
				$arrTurniere = unserialize($objSerie->turniere);
				foreach($arrTurniere as $item)
				{
					$turniere[$item['feldname']] = $item['name'];
				}
			}
			else $turniere = array();
		}

		// Turniernamen zurückgeben
		$namen = array();
		foreach($turniernamen as $turniername)
		{
			$namen[] = $turniere[$turniername];
		}
		return $namen;
	}

	/**
	 * Funktion getGruppe
	 * Liefert zum Feldnamen einer Gruppe den richtigen Namen 
	 * @param $turnierserie     int     ID der Turnierserie
	 * @param $feldname         string  Feldname der Gruppe, z.B. a
	 * @return string                   Richtiger Name, z.B. A-Gruppe
	 */
	static function getGruppe($turnierserie, $feldname)
	{
		static $gruppen;
		if(!isset($gruppen))
		{
			// Keine Gruppen vorhanden, darum DB abfragen
			$objSerie = \Database::getInstance()->prepare("SELECT gruppen FROM tl_internetschach WHERE id = ?")
			                                    ->execute($turnierserie);
			if($objSerie->numRows)
			{
				$arrGruppen = unserialize($objSerie->gruppen);
				foreach($arrGruppen as $item)
				{
					$gruppen[$item['feldname']] = $item['name'];
				}
			}
			else $gruppen = array();
		}

		return $gruppen[$feldname];
	}


	/**
	 * Funktion getTurnier
	 * Liefert zum Feldnamen eines Turniers den richtigen Namen 
	 * @param $turnierserie     int     ID der Turnierserie
	 * @param $feldname         string  Feldname des Turniers, z.B. v1
	 * @return string                   Richtiger Name, z.B. 1. Vorrunde
	 */
	static function getTurnier($turnierserie, $feldname)
	{
		static $turniere;
		if(!isset($turniere))
		{
			// Keine Turniere vorhanden, darum DB abfragen
			$objSerie = \Database::getInstance()->prepare("SELECT turniere FROM tl_internetschach WHERE id = ?")
			                                    ->execute($turnierserie);
			if($objSerie->numRows)
			{
				$arrTurniere = unserialize($objSerie->turniere);
				foreach($arrTurniere as $item)
				{
					$turniere[$item['feldname']] = $item['name'];
				}
			}
			else $turniere = array();
		}

		return $turniere[$feldname];
	}

	static function getTurniere($turnierserie, $turnierdaten)
	{
		static $turniere;
		if(!isset($turniere))
		{
			// Keine Turniere vorhanden, darum DB abfragen
			$objSerie = \Database::getInstance()->prepare("SELECT turniere FROM tl_internetschach WHERE id = ?")
			                                    ->execute($turnierserie);
			if($objSerie->numRows)
			{
				$arrTurniere = unserialize($objSerie->turniere);
				foreach($arrTurniere as $item)
				{
					$turniere[$item['feldname']] = $item['name'];
				}
			}
			else $turniere = array();
		}

		// Gewünschte Turniernamen laden
		$array = array();
		$temp = unserialize($turnierdaten);
		foreach($temp as $item)
		{
			$array[] = $turniere[$item]; 
		}
		return implode(', ', $array);
	}

	/**
	 * Funktion getAnmeldung
	 * Liefert zu einem ChessBase-Benutzernamen die Anmeldedaten
	 * @param $turnierserie     int     ID der Turnierserie
	 * @param $nick             string  Benutzername bei ChessBase
	 * @return array                    Array mit den Anmeldedaten
	 */
	static function getAnmeldung($turnierserie, $nick)
	{
		static $anmeldungen;

		if(!$turnierserie) return array();

		if(!$anmeldungen)
		{
			// Spielerdaten noch nicht eingelesen, deshalb aus DB abgfragen
			$objSpieler = \Database::getInstance()->prepare("SELECT * FROM tl_internetschach_anmeldungen WHERE pid = ?")
			                                      ->execute($turnierserie);
			if($objSpieler->numRows)
			{
				while($objSpieler->next())
				{
					$chessbase = explode(',', $objSpieler->chessbase); // ChessBase-Namen trennen
					if($chessbase)
					{
						for($x = 0; $x < count($chessbase); $x++)
						{
							$anmeldungen[] = array
							(
								'cb-name'    => strtolower(trim($chessbase[$x])),
								'name'       => $objSpieler->name,
								'verein'     => $objSpieler->verein,
								'dwz'        => $objSpieler->dwz ? $objSpieler->dwz : '',
								'fide-elo'   => $objSpieler->fideElo ? $objSpieler->fideElo : '',
								'fide-titel' => $objSpieler->fideTitel,
								'email'      => $objSpieler->email,
							);
						}
					}
				}
			}
		}

		// Benutzernamen suchen und Anmeldung zurückgeben
		foreach($anmeldungen as $item)
		{
			if($item['cb-name'] == strtolower($nick)) return $item;
		}
		return array();
	}

	/**
	 * Funktion TabelleToCSV
	 * Erstellt aus einem Tabellen-Array einen CSV-String 
	 * @param $tabelle          array   Array mit der Tabelle, Beispiel:
	 * [1] => Array
	 *     (
	 *         [platz] => 1
	 *         [benutzer] => Weltszmerc
	 *         [land] => POL
	 *         [rating] => 2171
	 *         [runde] => Array
	 *             (
	 *                 [0] => s 0/7
	 *                 [1] => w 1/8
	 *                 [2] => w 1/2
	 *                 [3] => s 1/23
	 *                 [4] => s 1/3
	 *                 [5] => w 1/10
	 *                 [6] => s ½/4
	 *                 [7] => s 1/6
	 *                 [8] => w 1/11
	 *             )
	 * 
	 *         [punkte] => 7.5 / 9
	 *         [wertung1] => 
	 *         [wertung2] => 
	 *         [realname] => Aab,Manfred
	 *     )
	 * @return string                   CSV-Ausgabe der Tabelle
	 */
	static function TabelleToCSV($tabelle)
	{
		// Schlüssel der Tabelle feststellen
		if($tabelle) $keys = array_keys($tabelle);
		else $keys = array();
		
		return print_r($keys, true);
	}

	/**
	 * Funktion TabelleToCSV
	 * Erstellt aus einem Tabellen-Array eine HTML-Tabelle mit den gewünschten Spalten 
	 * @param $turnierserie     int     ID der Turnierserie
	 * @param $tabelleID        int     ID der Tabelle
	 * @param $tabelle          array   Array mit der Tabelle, Beispiel:
	 * [1] => Array
	 *     (
	 *         [platz] => 1
	 *         [benutzer] => Weltszmerc
	 *         [land] => POL
	 *         [rating] => 2171
	 *         [runde] => Array
	 *             (
	 *                 [0] => s 0/7
	 *                 [1] => w 1/8
	 *                 [2] => w 1/2
	 *                 [3] => s 1/23
	 *                 [4] => s 1/3
	 *                 [5] => w 1/10
	 *                 [6] => s ½/4
	 *                 [7] => s 1/6
	 *                 [8] => w 1/11
	 *             )
	 * 
	 *         [punkte] => 7.5 / 9
	 *         [wertung1] => 
	 *         [wertung2] => 
	 *         [realname] => Aab,Manfred
	 *     )
	 * @param $spalten          array   Array mit den gewünschten Spalten
	 * @return string                   HTML-Ausgabe der Tabelle
	 */
	static function TabelleToHTML($turnierserie, $tabelleID, $tabelle, $spalten)
	{
		$spaltendefinition = $GLOBALS['TL_LANG']['tl_content']['internetschach_spalten_reference'];
		
		//$html .= print_r($tabelle, true);
		// Tabellenkopf schreiben
		$html = '<table>';
		$html .= '<thead>';
		$html .= '<tr>';
		if($spalten)
		{
			foreach($spalten as $spalte)
			{
				if($spalte == 'runden')
				{
					// Ergebnisse
					for($i = 1; $i <= count($tabelle[0][$spalte]); $i++)
					{
						$html .= '<th>';
						$html .= $i;
						$html .= '</th>';
					}
				}
				elseif($spalte == 'titel+name')
				{
					// Besondere Spalte für Ausgabe des Namens mit FIDE-Titel
					$html .= '<th>';
					$html .= $spaltendefinition['name'];
					$html .= '</th>';
				}
				elseif($spalte == 'qualification')
				{
					// Besondere Spalte für die Ausgabe der Qualifikation für das Finale
					$html .= '<th>';
					$html .= 'Qual.';
					$html .= '</th>';
				}
				else 
				{
					// Normale Spalte
					$html .= '<th>';
					$html .= $spaltendefinition[$spalte];
					$html .= '</th>';
				}
			}
		}
		$html .= '</tr>';
		$html .= '</thead>';
		$html .= '<tbody>';

		if($spalten)
		{
			if(in_array('qualification', $spalten))
			{
				// Qualifikationen als Spalte in die Tabelle eintragen
				$tabelle = self::getQualifikationen($turnierserie, $tabelleID, $tabelle);
			}
		}

		// Tabellenkörper schreiben
		for($zeile = 1; $zeile < count($tabelle); $zeile++)
		{
			$html .= '<tr>';
			$anmeldung = self::getAnmeldung($turnierserie, $tabelle[$zeile]['cb-name']); // Anmeldedaten des Spielers laden
			if($spalten)
			{
				foreach($spalten as $spalte)
				{
					if($spalte == 'runden')
					{
						// Ergebnisse
						for($i = 0; $i < count($tabelle[$zeile][$spalte]); $i++)
						{
							$html .= '<td class="result">';
							$html .= $tabelle[$zeile][$spalte][$i];
							$html .= '</td>';
						}
					}
					elseif($spalte == 'titel+name')
					{
						// Besondere Spalte für Ausgabe des Namens mit FIDE-Titel
						$html .= '<td>';
						$html .= ($anmeldung['fide-titel'] ? $anmeldung['fide-titel'].' ' : '').\Schachbulle\ContaoHelperBundle\Classes\Helper::NameDrehen($anmeldung['name']);
						$html .= '</td>';
					}
					else 
					{
						// Normale Spalte
						$html .= '<td>';
						if(isset($tabelle[$zeile][$spalte]))
						{
							$html .= $tabelle[$zeile][$spalte];
						}
						else
						{
							// Spalte ist nicht in der Tabelle
							$html .= $anmeldung[$spalte];
						}
						$html .= '</td>';
					}
				}
			}
			$html .= '</tr>';
		}
		
		// Tabellenfuß schreiben
		$html .= '</tbody>';
		$html .= '</table>';
		return $html;
	}

	/**
	 * Funktion getQualifikationen
	 * Trägt in eine Tabelle (Array) die Qualifikationen als Feld ein
	 * @param $turnierserie     int     ID der Turnierserie
	 * @param $tabelleID        int     ID der aktuellen Tabelle
	 * @param $tabelle          array   Array mit den Tabellendaten
	 * @param $gruppe           string  Feldname der aktuellen Gruppe der Tabelle
	 * @return array                    Modifiziertes Array mit den Tabellendaten
	 */
	static function getQualifikationen($turnierserie, $tabelleID, $tabelle)
	{
		if(!$turnierserie || !$tabelleID) return $tabelle; // Tabelle unmodifiziert zurückgeben

		// Turnierserie einlesen
		$objSerie = \Database::getInstance()->prepare("SELECT * FROM tl_internetschach WHERE id = ?")
		                                    ->execute($turnierserie);
		if($objSerie->numRows)
		{
			// Turniere und Gruppen laden
			$turnierplan = unserialize($objSerie->turniere);
			$gruppenplan = unserialize($objSerie->gruppen);
		}
		//print_r($turnierplan);

		// Alle restlichen Tabellen einlesen
		$objTabellen = \Database::getInstance()->prepare("SELECT * FROM tl_internetschach_tabellen WHERE pid = ?")
		                                       ->execute($turnierserie);
		$tabellenplan = array(); // Nimmt die Tabellendaten auf
		$aktuelleGruppe = '';
		if($objTabellen->numRows)
		{
			while($objTabellen->next())
			{
				if($objTabellen->id == $tabelleID) $aktuelleGruppe = $objTabellen->gruppe; // Gruppe der aktuellen Tabelle sichern
				// Spieltermin und Finalstatus suchen
				$spieltermin = 0;
				$finale = false;
				foreach($turnierplan as $item)
				{
					if($item['feldname'] == $objTabellen->turnier)
					{
						$spieltermin = $item['termin'];
						$finale = $item['finale'];
						break;
					}
				}
				$tabellenplan[] = array
				(
					'id'               => $objTabellen->id,
					'daten'            => $objTabellen->importArray,
					'turnier'          => $objTabellen->turnier,
					'gruppe'           => $objTabellen->gruppe,
					'finale'           => $finale,
					'ungewertet'       => $objTabellen->ungewertet,
					'disqualifikation' => $objTabellen->disqualifikation,
					'spieltermin'      => $spieltermin,
				);
			}
		}

		$benutzer = array(); // Array mit dem Benutzernamen als Index und dem Turnier, wo die Qualifikation erreicht wurde, als Wert
		//print_r($tabellenplan);
		
		// Tabelle modifizieren
		for($i = 0; $i < count($tabelle); $i++)
		{
			$tabelle[$i]['qualification'] = '';
		}

		return $tabelle;
	}

}
