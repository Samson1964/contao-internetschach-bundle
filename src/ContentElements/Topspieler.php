<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * @package   chesstable
 * Version    1.0.0
 * @author    Frank Hoppe
 * @license   GNU/LGPL
 * @copyright Frank Hoppe 2013
 */

namespace Schachbulle\ContaoInternetschachBundle\ContentElements;

class Topspieler extends \ContentElement
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'ce_internetschach_topspieler';

	/**
	 * Generate the module
	 */
	protected function compile()
	{
		// Anzuzeigende Gruppen/Turniere in Array packen
		$view_turniere = unserialize($this->internetschach_turniere);
		$view_gruppen = unserialize($this->internetschach_gruppen);
		// $this->internetschach_topanzahl
		// $this->internetschach_punktplaetze
		
		$Benutzer = array();

		// Tabellen laden
		$anmeldung = array();
		$objTabellen = \Database::getInstance()->prepare('SELECT * FROM tl_internetschach_tabellen WHERE pid = ? AND published = ?')
		                                        ->execute($this->internetschach, 1);

		if($objTabellen->numRows)
		{
			while($objTabellen->next())
			{
				if(in_array($objTabellen->turnier, $view_turniere) && in_array($objTabellen->gruppe, $view_gruppen))
				{
					$tabelle = unserialize($objTabellen->importArray); // Tabelle extrahieren
					// Benutzer aus Tabelle einlesen
					for($i = 1; $i < count($tabelle); $i++)
					{
						$wertungspunkte = $this->internetschach_punktplaetze - $tabelle[$i]['platz'] + 1;
						$chessbaseName = strtolower($tabelle[$i]['cb-name']);
						$Benutzer[$chessbaseName][$objTabellen->turnier] = array
						(
							'platz'          => $tabelle[$i]['platz'],
							'wertungspunkte' => $wertungspunkte > 0 ? $wertungspunkte : 0
						);
					}
				}
			}
		}

		// Gesamtwertungspunkte berechnen
		$Wertung = array();
		foreach($Benutzer as $Benutzername => $valueArr)
		{
			$gesamt = 0;
			foreach($valueArr as $turnier => $itemArr)
			{
				$gesamt += $itemArr['wertungspunkte'];
			}
			$Wertung[] = array
			(
				'benutzer' => $Benutzername,
				'punkte'   => $gesamt
			);
		}
		
		$Wertung = \Schachbulle\ContaoHelperBundle\Classes\Helper::sortArrayByFields($Wertung, array('punkte' => SORT_DESC));

		//echo "<pre>";
		//print_r($Wertung);
		//echo "</pre>";
		
		$content = '<table width="100%">';
		$content .= '<tr>';
		$content .= '<th>Pl.</th>';
		$content .= '<th>Name</th>';
		$content .= '<th>Titel</th>';
		$content .= '<th>DWZ</th>';
		$content .= '<th>Verein</th>';
		$content .= '<th>Punkte</th>';
		foreach($view_turniere as $item)
		{
			$content .= '<th>Platz<br>'.$item.'</th>';
		}
		$content .= '</tr>';
		$platz = 0;
		for($i = 0; $i < count($Wertung); $i++)
		{
			$content .= '<tr>';
			// Anmeldedaten suchen
			$Anmeldung = \Schachbulle\ContaoInternetschachBundle\Classes\Helper::getAnmeldung($this->internetschach, $Wertung[$i]['benutzer']);
			if($Anmeldung)
			{
				$platz++;
				$content .= '<td>'.$platz.'</td>';
				$content .= '<td>'.$Anmeldung['name'].'</td>';
				$content .= '<td>'.$Anmeldung['fide-titel'].'</td>';
				$content .= '<td>'.$Anmeldung['dwz'].'</td>';
				$content .= '<td>'.$Anmeldung['verein'].'</td>';
				$content .= '<td>'.$Wertung[$i]['punkte'].'</td>';
				foreach($view_turniere as $turnier)
				{
					$content .= '<td>';
					if($Benutzer[$Wertung[$i]['benutzer']][$turnier]) $content .= $Benutzer[$Wertung[$i]['benutzer']][$turnier]['platz'];
					$content .= '</td>';
				}
			}
			$content .= '</tr>';
			if($platz == $this->internetschach_topanzahl) break;
		}
		$content .= '</table>';

		// Template ausgeben
		$this->Template = new \FrontendTemplate($this->strTemplate);
		$this->Template->class = 'ce_internetschach';
		$this->Template->content = $content;

	}
}
