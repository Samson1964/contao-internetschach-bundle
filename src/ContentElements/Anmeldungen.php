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
		$anmeldungen = array();
		$objMeldungen = \Database::getInstance()->prepare('SELECT * FROM tl_internetschach_anmeldungen WHERE pid = ? AND published = ? ORDER BY dwz DESC')
		                                        ->execute($this->internetschach, 1);

		if($objMeldungen->numRows)
		{
			$nr = 0;
			while($objMeldungen->next())
			{
				if(self::Verifizierung($objMeldungen->turniere, $objMeldungen->gruppe))
				{
					$anmeldungen[$nr] = $objMeldungen->row();
					$anmeldungen[$nr]['nr'] = $nr+1;
					$nr++;
				}
			}
		}


		//echo "<pre>";
		//print_r($anmeldungen);
		//echo "</pre>";
		$total = count($anmeldungen);
		$limit = $total;
		$offset = 0;

		// Paginierung bauen
		if($this->perPage > 0)
		{
			// Get the current page
			$id = 'page_is' . $this->id;
			$page = (\Input::get($id) !== null) ? \Input::get($id) : 1;

			// Do not index or cache the page if the page number is outside the range
			if($page < 1 || $page > max(ceil($total/$this->perPage), 1))
			{
				throw new PageNotFoundException('Page not found: ' . Environment::get('uri'));
			}

			// Set limit and offset
			$offset = ($page - 1) * $this->perPage;
			$limit = min($this->perPage + $offset, $total);

			$objPagination = new \Pagination($total, $this->perPage, \Config::get('maxPaginationLinks'), $id);
			$this->Template->pagination = $objPagination->generate("\n ");
		}

		$content = '<table class="ce_table" width="100%">';
		$content .= '<tr>';
		$content .= '<th>Nr.</th>';
		$content .= '<th>Name</th>';
		$content .= '<th>Titel</th>';
		$content .= '<th>DWZ</th>';
		$content .= '<th>Verein</th>';
		if($this->internetschach_viewturniere) $content .= '<th>Turniere</th>';
		if($this->internetschach_viewgruppen)$content .= '<th>Gruppe</th>';
		$content .= '</tr>';
		// Datensätze entsprechend Paginierung ausgeben
		for($i = $offset; $i < $limit; $i++)
		{
			if($anmeldungen[$i]['checked']) $content .= '<tr class="checked">';
			else $content .= '<tr class="unchecked">';
			$content .= '<td>'.$anmeldungen[$i]['nr'].'</td>';
			$content .= '<td>'.$anmeldungen[$i]['name'].'</td>';
			$content .= '<td>'.$anmeldungen[$i]['fideTitel'].'</td>';
			$content .= '<td>'.($anmeldungen[$i]['dwz'] ? $anmeldungen[$i]['dwz'] : '-').'</td>';
			$content .= '<td>'.$anmeldungen[$i]['verein'].'</td>';
			if($this->internetschach_viewturniere) $content .= '<td>'.\Schachbulle\ContaoInternetschachBundle\Classes\Helper::getTurniere($this->internetschach, $anmeldungen[$i]['turniere']).'</td>';
			if($this->internetschach_viewgruppen) $content .= '<td>'.\Schachbulle\ContaoInternetschachBundle\Classes\Helper::getGruppe($this->internetschach, $anmeldungen[$i]['gruppe']).'</td>';
			$content .= '</tr>';
		}
		$content .= '</table>';

		// Template ausgeben
		$this->Template->class = 'ce_internetschach';
		$this->Template->content = ($total < 1 ? '<p>Keine Anmeldungen gefunden</p>' : $content);

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
