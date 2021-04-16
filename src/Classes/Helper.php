<?php

namespace Schachbulle\ContaoInternetschachBundle\Classes;

class Helper
{

	var $anmeldungenArray = array();
	var $gruppen = array(); // Enthält die Gruppen einer Turnierserie

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
		if(!empty($gruppen[0]['name']))
		{
			// Es ist mind. eine Gruppe definiert
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
		}
		else
		{
			// Es sind keine Gruppen definiert
			return '';
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
	 * Funktion getPreis
	 * Liefert zu einem Array von Preis-ID die Namen der Preise
	 * @param $preiseArr        array   ID's der gewonnenen Preise
	 * @return string                   Namen der Preise, getrennt von <br>
	 */
	static function getPreis($preiseArr)
	{
		static $Preise;

		if(!$Preise)
		{
			// Preise einlesen
			$objPreise = \Database::getInstance()->prepare("SELECT * FROM tl_internetschach_preise WHERE published = ?")
			                                     ->execute(1);
			if($objPreise->numRows)
			{
				while($objPreise->next())
				{
					$Preise[$objPreise->id] = $objPreise->name;
				}
			}
		}

		// Namen der Preise ermitteln
		$tempArr = array();
		foreach($preiseArr as $id)
		{
			$tempArr[] = $Preise[$id];
		}
		return implode('<br>', $tempArr);
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
	 * @param $objTurnierserie  object  Objekt der Turnierserie
	 * @param $objTabelle       object  Objekt der Tabelle
	 * @param $spalten          array   Array mit den gewünschten Spalten
	 * @param $anzahl           int     Anzahl der auszugebenden Plätze
	 * @return string                   HTML-Ausgabe der Tabelle
	 */
	static function TabelleToHTML($objTurnierserie, $objTabelle, $spalten, $anzahl)
	{
		$tabelle = unserialize($objTabelle->importArray); // Tabelle von serialisiertem String in Array umwandeln
		$spaltendefinition = $GLOBALS['TL_LANG']['tl_content']['internetschach_spalten_reference'];
		$class = array(); // Feld für die CSS-Klassennamen, Index ist der Spaltenname
		$disqualifiziert = \Schachbulle\ContaoHelperBundle\Classes\Helper::StringToArray($objTabelle->disqualifikation);
		$ungewertet = \Schachbulle\ContaoHelperBundle\Classes\Helper::StringToArray($objTabelle->ungewertet);

		//$html .= print_r($tabelle, true);
		// Tabellenkopf schreiben
		$html = '<table>';
		$html .= '<thead>';
		$html .= '<tr>';
		if($spalten)
		{
			$qualifikationsspalte = in_array('qualification', $spalten); // Status Qualifikationsspalte sichern
			foreach($spalten as $spalte)
			{
				switch($spalte)
				{
					case 'platz'        : $class[$spalte] = 'platz'; break;
					case 'cb-name'      : $class[$spalte] = 'cbname'; break;
					case 'cb-land'      : $class[$spalte] = 'land'; break;
					case 'cb-rating'    : $class[$spalte] = 'rating'; break;
					case 'punkte'       : $class[$spalte] = 'punkte'; break;
					case 'wertung1'     : $class[$spalte] = 'wertung'; break;
					case 'wertung2'     : $class[$spalte] = 'wertung'; break;
					case 'runden'       : $class[$spalte] = 'result'; break;
					case 'name'         : $class[$spalte] = 'name'; break;
					case 'titel+name'   : $class[$spalte] = 'name'; break;
					case 'dwz'          : $class[$spalte] = 'rating'; break;
					case 'verein'       : $class[$spalte] = 'verein'; break;
					case 'verein_kurz'  : $class[$spalte] = 'verein_kurz'; break;
					case 'fide-titel'   : $class[$spalte] = 'ftitel'; break;
					case 'fide-elo'     : $class[$spalte] = 'rating'; break;
					case 'email'        : $class[$spalte] = 'email'; break;
					case 'qualification': $class[$spalte] = 'qualifikation'; break;
					case 'prices'       : $class[$spalte] = 'prices'; break;
				}
				
				if($spalte == 'runden')
				{
					// Ergebnisse
					for($i = 1; $i <= count($tabelle[0][$spalte]); $i++)
					{
						$html .= '<th class="'.$class[$spalte].'">';
						$html .= $i;
						$html .= '</th>';
					}
				}
				elseif($spalte == 'titel+name')
				{
					// Besondere Spalte für Ausgabe des Namens mit FIDE-Titel
					$html .= '<th class="'.$class[$spalte].'">';
					$html .= $spaltendefinition['name'];
					$html .= '</th>';
				}
				elseif($spalte == 'verein_kurz')
				{
					// Besondere Spalte für Ausgabe des Namens mit FIDE-Titel
					$html .= '<th class="'.$class[$spalte].'">';
					$html .= $spaltendefinition['verein'];
					$html .= '</th>';
				}
				elseif($spalte == 'qualification')
				{
					// Besondere Spalte für die Ausgabe der Qualifikation für das Finale
					$html .= '<th class="'.$class[$spalte].'">';
					$html .= 'Qual.';
					$html .= '</th>';
				}
				else 
				{
					// Normale Spalte
					$html .= '<th class="'.$class[$spalte].'">';
					$html .= $spaltendefinition[$spalte];
					$html .= '</th>';
				}
			}
		}
		$html .= '</tr>';
		$html .= '</thead>';
		$html .= '<tbody>';

		//echo "<pre>";
		//print_r($tabelle);
		//echo "</pre>";
		// Tabellenkörper schreiben
		for($zeile = 1; $zeile < count($tabelle); $zeile++)
		{
			// CSS-Klassen für Zeile eintragen
			$trclass = '';
			if(in_array($zeile+1, $disqualifiziert)) $trclass .= 'disqualifiziert';
			if(in_array($zeile+1, $ungewertet)) $trclass = $trclass ? $trclass.' ungewertet' : 'ungewertet';
			if($tabelle[$zeile]['qualification'] && $qualifikationsspalte) $trclass = $trclass ? $trclass.' qualifiziert' : 'qualifiziert';

			$html .= '<tr class="'.$trclass.'">';
			$anmeldung = self::getAnmeldung($objTurnierserie->id, $tabelle[$zeile]['cb-name']); // Anmeldedaten des Spielers laden
			if($spalten)
			{
				foreach($spalten as $spalte)
				{
					if($spalte == 'runden')
					{
						// Ergebnisse
						for($i = 0; $i < count($tabelle[$zeile][$spalte]); $i++)
						{
							$html .= '<td class="'.$class[$spalte].'">';
							$html .= $tabelle[$zeile][$spalte][$i];
							$html .= '</td>';
						}
					}
					elseif($spalte == 'titel+name')
					{
						// Besondere Spalte für Ausgabe des Namens mit FIDE-Titel
						$html .= '<td class="'.$class[$spalte].'">';
						$html .= ($anmeldung['fide-titel'] ? $anmeldung['fide-titel'].' ' : '').\Schachbulle\ContaoHelperBundle\Classes\Helper::NameDrehen($anmeldung['name']);
						$html .= '</td>';
					}
					elseif($spalte == 'verein_kurz')
					{
						// Besondere Spalte für gekürzten Vereinsnamen
						$html .= '<td class="'.$class[$spalte].'">';
						$html .= \Schachbulle\ContaoHelperBundle\Classes\Helper::StringKuerzen($anmeldung['verein'], 20);
						$html .= '</td>';
					}
					elseif($spalte == 'prices')
					{
						// Besondere Spalte für den Preis
						$html .= '<td class="'.$class[$spalte].'">';
						$html .= \Schachbulle\ContaoInternetschachBundle\Classes\Helper::getPreis($tabelle[$zeile][$spalte]);
						$html .= '</td>';
					}
					else 
					{
						// Normale Spalte
						$html .= '<td class="'.$class[$spalte].'">';
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
			// Wenn max. Anzahl der Plätze erreicht ist, dann abbrechen
			if($anzahl && $anzahl == $zeile) break;
		}
		
		// Tabellenfuß schreiben
		$html .= '</tbody>';
		$html .= '</table>';
		return $html;
	}

	/**
	 * Funktion getBenutzernamen
	 * Erstellt aus dem String mit den Benutzernamen ein Array
	 * @param $namen            string  z.B. "Jonas Eilenberg, JonasEilenberg"
	 * @return array                    array('Jonas Eilenberg', 'JonasEilenberg')
	 */
	static function getBenutzernamen($namen)
	{
		$neu = explode(',', $namen);
		$array = array();
		foreach($neu as $item)
		{
			$array[] = trim($item);
		}
		return $array;
	}

	/**
	 * Funktion ChessbaseCheck
	 * Fragt die ChessBase-API nach dem Status des Benutzernamens ab
	 * @param $benutzername    string   Benutzername
	 * @param $spielerId       integer  ID des Spielers in tl_internetschach_spieler
	 * @param $code            string   siehe return
	 * @return                 array    Wenn $code = Nummer, dann keine Auswertung der ersten 2 Parameter
	 *                                  array
	 *                                  (
	 *                                    'code'  => $code,
	 *                                    'text'  => 'Hinweistext',
	 *                                    'error' => true/false
	 *                                  )
	 * @return                 array    Wenn $code = false (Standard), dann Auswertung der ersten 2 Parameter
	 *                                  array
	 *                                  (
	 *                                    'code'  => 1/2/3/4/5,
	 *                                    'text'  => 'Hinweistext',
	 *                                    'error' => true/false
	 *                                  )
	 *
	 * {
	 *  "account":"Samson2",
	 *  "last":"Hoppe",
	 *  "first":"Frank",
	 *  "pic":"https://users.chessbase.com:8081/Pics/Default/S/Samson2.jpg",
	 *  "success":true,
	 *  "online":false
	 * }
	 */
	static function ChessbaseCheck($benutzername, $spielerId, $code = false)
	{
		if($code == false && $benutzername)
		{
			// Benutzername prüfen, wenn vorhanden
			$response = file_get_contents('https://play.chessbase.com/de/info?account='.rawurlencode($benutzername));
			$chessbase = json_decode($response);

			if($chessbase->success)
			{
				// Benutzername vorhanden, realen Namen laden
				if($spielerId)
				{
					// Spieler laden
					$player = \Database::getInstance()->prepare("SELECT * FROM tl_internetschach_spieler WHERE id = ?")
					                                  ->execute($spielerId);
					if($player->numRows)
					{
						$spieler = explode(',', $player->name); // Mustermann,Hans,Prof.Dr. trennen
						if($spieler[0] == $chessbase->last && $spieler[1] == $chessbase->first)
						{
							$code = 3;
						}
						else
						{
							$code = 4;
						}
					}
					else
					{
						$code = 5;
					}
				}
				else
				{
					// Es wurde noch kein Spieler ausgewählt
					$code = 2;
				}
			}
			else
			{
				// Benutzername nicht vorhanden
				$code = 1;
			}
		}

		if($code)
		{
			// Code wurde übergeben, Array zusammenbauen
			switch($code)
			{
				case 1:
					$text = 'ChessBase-Benutzername nicht gefunden!';
					$error = true;
					break;
				case 2:
					$text = 'Kein Spieler im Formular ausgewählt!';
					$error = true;
					break;
				case 3:
					$text = 'ChessBase-Benutzername gefunden! Realer Name im ChessBase-Konto okay.';
					$error = false;
					break;
				case 4:
					$text = 'ChessBase-Benutzername gefunden! Realer Name im ChessBase-Konto weicht ab: '.$cbkontoname;
					$text = 'ChessBase-Benutzername gefunden! Realer Name im ChessBase-Konto weicht ab.';
					$error = true;
					break;
				case 5:
					$text = 'Spieler im Formular konnte nicht gefunden werden!';
					$error = true;
					break;
				default:
			}
		}

		// Ergebnis zurückgeben
		return array
		(
			'code'  => $code,
			'text'  => $text,
			'error' => $error
		);

	}

}
