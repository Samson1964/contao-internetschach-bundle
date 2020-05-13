<?php

namespace Schachbulle\ContaoInternetschachBundle\Classes;

class Helper
{

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

}
