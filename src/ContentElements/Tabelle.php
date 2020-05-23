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

class Tabelle extends \ContentElement
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'ce_internetschach_tabelle';

	/**
	 * Generate the module
	 */
	protected function compile()
	{
		global $objPage;

		// Turnierserie einlesen
		$objMain = \Database::getInstance()->prepare('SELECT * FROM tl_internetschach WHERE id = ?')
		                                   ->execute($this->internetschach);

		// Tabelle einlesen
		$objTabelle = \Database::getInstance()->prepare('SELECT * FROM tl_internetschach_tabellen WHERE id = ?')
		                                      ->execute($this->internetschach_tabelle);

		if($objTabelle->numRows)
		{
			$spalten = unserialize($this->internetschach_spalten); // Gewünschte Spalten von serialisiertem String in Array umwandeln
			// Tabelle in HTML umwandeln und dabei Spalten ergänzen/entfernen
			$content = \Schachbulle\ContaoInternetschachBundle\Classes\Helper::TabelleToHTML($objMain, $objTabelle, $spalten);
		}

		// Template ausgeben
		$this->Template = new \FrontendTemplate($this->strTemplate);
		$this->Template->class = 'ce_internetschach_tabelle';
		$this->Template->default_css = true;
		$this->Template->content = $content;

		return;

	}
}
