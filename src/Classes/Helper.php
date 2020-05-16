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
	 * Funktion getRealname
	 * Liefert zu einem ChessBase-Benutzernamen für eine Turnierserie den richtigen Namen 
	 * @param $turnierserie     int     ID der Turnierserie
	 * @param $nick             string  Benutzername bei ChessBase
	 * @return string                   Richtiger Name
	 */
	static function getRealname($turnierserie, $nick)
	{
		static $anmeldungen;

		if(!$turnierserie) return '';

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
								'chessbase' => trim($chessbase[$x]),
								'name'      => $objSpieler->name,
								'verein'    => $objSpieler->verein,
								'dwz'       => $objSpieler->dwz,
								'fideTitel' => $objSpieler->fideTitel
							);
						}
					}
				}
			}
		}

		// Benutzernamen suchen und Realnamen zurückgeben
		foreach($anmeldungen as $item)
		{
			if($item['chessbase'] == $nick) return $item['name'];
		}
		return '';
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
}
