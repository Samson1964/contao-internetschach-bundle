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

class Anmeldungen extends \ContentElement
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'ce_internetschach_anmeldungen';
	var $wunsch_turniere = array();
	var $wunsch_gruppen = array();

	/**
	 * Generate the module
	 */
	protected function compile()
	{
		// Gewünschte Gruppen/Turniere in Array packen
		$this->wunsch_turniere = unserialize($this->internetschach_turniere);
		$this->wunsch_gruppen = unserialize($this->internetschach_gruppen);

		// Anmeldungen entsprechend Filter laden
		$anmeldung = array();
		$objMeldungen = \Database::getInstance()->prepare('SELECT * FROM tl_internetschach_anmeldungen WHERE pid = ? AND published = ? ORDER BY dwz DESC')
		                                        ->execute($this->internetschach, 1);
		if($objMeldungen->numRows)
		{
			$content = '<table>';
			$content .= '<tr>';
			$content .= '<th>Nr.</th>';
			$content .= '<th>Name</th>';
			$content .= '<th>Titel</th>';
			$content .= '<th>DWZ</th>';
			$content .= '<th>Verein</th>';
			$content .= '</tr>';
			$nr = 0;
			while($objMeldungen->next())
			{
				if(self::Verifizierung($objMeldungen->turniere, $objMeldungen->gruppen))
				{
					$nr++;
					if($objMeldungen->checked) $content .= '<tr>';
					else $content .= '<tr style="background-color:#FFD7D7">';
					$content .= '<td>'.$nr.'</td>';
					$content .= '<td>'.$objMeldungen->name.'</td>';
					$content .= '<td>'.$objMeldungen->fideTitel.'</td>';
					$content .= '<td>'.$objMeldungen->dwz.'</td>';
					$content .= '<td>'.$objMeldungen->turniere.'</td>';
					$content .= '</tr>';
				}
			}
			$content .= '</table>';
		}

		// Template ausgeben
		$this->Template = new \FrontendTemplate($this->strTemplate);
		$this->Template->class = 'ce_internetschach';
		$this->Template->content = $content;

	}

	function Verifizierung($gemeldete_turniere, $gemeldete_gruppen)
	{
		$gemeldete_turniere = unserialize($gemeldete_turniere); // Array mit vom Spieler gemeldeten Turnieren
		$gemeldete_gruppen = unserialize($gemeldete_gruppen); // Array mit vom Spieler gemeldeten Gruppen

		// Gewünschtes Turnier in gemeldeten Turnieren suchen
		foreach($this->wunsch_turniere as $turnier)
		{
			if(in_array($turnier, (array)$gemeldete_turniere)) return true;
		}

		return false;
	}

}
