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
	var $view_turniere = array();
	var $view_gruppen = array();

	/**
	 * Generate the module
	 */
	protected function compile()
	{
		// Anzuzeigende Gruppen/Turniere in Array packen
		$this->view_turniere = unserialize($this->internetschach_turniere);
		$this->view_gruppen = unserialize($this->internetschach_gruppen);
		
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
			if($this->internetschach_viewturniere) $content .= '<th>Turniere</th>';
			if($this->internetschach_viewgruppen)$content .= '<th>Gruppe</th>';
			$content .= '</tr>';
			$nr = 0;
			while($objMeldungen->next())
			{
				if(self::Verifizierung($objMeldungen->turniere, $objMeldungen->gruppe))
				{
					$nr++;
					if($objMeldungen->checked) $content .= '<tr class="checked">';
					else $content .= '<tr class="unchecked">';
					$content .= '<td>'.$nr.'</td>';
					$content .= '<td>'.$objMeldungen->name.'</td>';
					$content .= '<td>'.$objMeldungen->fideTitel.'</td>';
					$content .= '<td>'.($objMeldungen->dwz ? $objMeldungen->dwz : '-').'</td>';
					$content .= '<td>'.$objMeldungen->verein.'</td>';
					if($this->internetschach_viewturniere) $content .= '<td>'.\Schachbulle\ContaoInternetschachBundle\Classes\Helper::getTurniere($this->internetschach, $objMeldungen->turniere).'</td>';
					if($this->internetschach_viewgruppen) $content .= '<td>'.\Schachbulle\ContaoInternetschachBundle\Classes\Helper::getGruppe($this->internetschach, $objMeldungen->gruppe).'</td>';
					$content .= '</tr>';
				}
			}
			$content .= '</table>';
		}

		// Paginate the result of not randomly sorted (see #8033)
		if($this->perPage > 0)
		{
			// Get the current page
			$id = 'page_g' . $this->id;
			$page = \Input::get($id) ? 1 : 3;
        //
		//	// Do not index or cache the page if the page number is outside the range
		//	if ($page < 1 || $page > max(ceil($total/$this->perPage), 1))
		//	{
		//		throw new PageNotFoundException('Page not found: ' . Environment::get('uri'));
		//	}
        //
		//	// Set limit and offset
		//	$offset = ($page - 1) * $this->perPage;
		//	$limit = min($this->perPage + $offset, $total);
        //
		//	$objPagination = new Pagination($total, $this->perPage, Config::get('maxPaginationLinks'), $id);
		//	$this->Template->pagination = $objPagination->generate("\n  ");
		}

		// Template ausgeben
		$this->Template = new \FrontendTemplate($this->strTemplate);
		$this->Template->class = 'ce_internetschach';
		$this->Template->content = $content;

	}

	function Verifizierung($gemeldete_turniere, $gemeldete_gruppe)
	{
		$arr_gemeldete_turniere = unserialize($gemeldete_turniere); // Array mit vom Spieler gemeldeten Turnieren
		//$arr_gemeldete_gruppe = unserialize($gemeldete_gruppe); // Array mit vom Spieler gemeldeter Gruppe

		// Gemeldete Turniere in anzuzeigenden Turnieren suchen
		$foundTurnier = false;
		foreach($this->view_turniere as $turnier)
		{
			if(in_array($turnier, (array)$arr_gemeldete_turniere))
			{
				$foundTurnier = true;
				break;
			}
		}

		// Gemeldete Gruppe in anzuzeigende Gruppen suchen
		$foundGruppe = false;
		if($this->view_gruppen)
		{
			foreach($this->view_gruppen as $gruppe)
			{
				if($gruppe == $gemeldete_gruppe)
				{
					$foundGruppe = true;
					break;
				}
			}
		}
		else
		{
			$foundGruppe = true;
		}

		if($foundTurnier && $foundGruppe) return true;
		return false;
	}

}
