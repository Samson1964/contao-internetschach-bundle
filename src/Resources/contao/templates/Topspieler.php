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
		
		// Anmeldungen entsprechend Filter laden
		$anmeldung = array();
		$objMeldungen = \Database::getInstance()->prepare('SELECT * FROM tl_internetschach_anmeldungen WHERE pid = ? AND published = ? ORDER BY dwz DESC')
		                                        ->execute($this->internetschach, 1);
		if($objMeldungen->numRows)
		{
			$content = '<table width="100%">';
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
				$nr++;
				if($objMeldungen->checked) $content .= '<tr class="checked">';
				else $content .= '<tr class="unchecked">';
				$content .= '<td>'.$nr.'</td>';
				$content .= '<td>'.$objMeldungen->name.'</td>';
				$content .= '<td>'.$objMeldungen->fideTitel.'</td>';
				$content .= '<td>'.($objMeldungen->dwz ? $objMeldungen->dwz : '-').'</td>';
				$content .= '<td>'.$objMeldungen->verein.'</td>';
				$content .= '</tr>';
			}
			$content .= '</table>';
		}

		// Template ausgeben
		$this->Template = new \FrontendTemplate($this->strTemplate);
		$this->Template->class = 'ce_internetschach';
		$this->Template->content = $content;

	}
}
