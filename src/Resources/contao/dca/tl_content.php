<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * @package   fen
 * @author    Frank Hoppe
 * @license   GNU/LGPL
 * @copyright Frank Hoppe 2013
 */

/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['internetschach_formular'] = '{type_legend},type,headline;{internetschach_legend},internetschach;{protected_legend:hide},protected;{expert_legend:hide},guest,cssID,space;{invisible_legend:hide},invisible,start,stop';
$GLOBALS['TL_DCA']['tl_content']['palettes']['internetschach_anmeldungen'] = '{type_legend},type,headline;{internetschach_legend},internetschach;{internetschach_anmeldungen_legend},internetschach_turniere,internetschach_gruppen,internetschach_viewturniere,internetschach_viewgruppen;{protected_legend:hide},protected;{expert_legend:hide},guest,cssID,space;{invisible_legend:hide},invisible,start,stop';
$GLOBALS['TL_DCA']['tl_content']['palettes']['internetschach_tabelle'] = '{type_legend},type,headline;{internetschach_legend},internetschach;{internetschach_tabelle_legend},internetschach_tabelle,internetschach_spalten,internetschach_plaetze,internetschach_css;{protected_legend:hide},protected;{expert_legend:hide},guest,cssID,space;{invisible_legend:hide},invisible,start,stop';
$GLOBALS['TL_DCA']['tl_content']['palettes']['internetschach_topspieler'] = '{type_legend},type,headline;{internetschach_legend},internetschach;{internetschach_anmeldungen_legend},internetschach_turniere,internetschach_gruppen;{internetschach_topspieler_legend},internetschach_topanzahl,internetschach_punktplaetze;{protected_legend:hide},protected;{expert_legend:hide},guest,cssID,space;{invisible_legend:hide},invisible,start,stop';

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['internetschach'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['internetschach'],
	'exclude'                 => true,
	'options_callback'        => array('tl_content_internetschach', 'getTurnierserie'),
	'inputType'               => 'select',
	'eval'                    => array
	(
		'mandatory'           => true,
		'includeBlankOption'  => true,
		'blankOptionLabel'    => $GLOBALS['TL_LANG']['tl_content']['internetschach_blanklabel'],
		'multiple'            => false,
		'chosen'              => true,
		'submitOnChange'      => true,
		'tl_class'            => 'long'
	),
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['internetschach_turniere'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['internetschach_turniere'],
	'exclude'                 => true,
	'inputType'               => 'checkboxWizard',
	'options_callback'        => array('tl_content_internetschach', 'getTurniere'),
	'eval'                    => array
	(
		'mandatory'           => false,
		'multiple'            => true,
		'tl_class'            => 'w50'
	),
	'sql'                     => "blob NULL"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['internetschach_gruppen'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['internetschach_gruppen'],
	'exclude'                 => true,
	'inputType'               => 'checkboxWizard',
	'options_callback'        => array('tl_content_internetschach', 'getGruppen'),
	'eval'                    => array
	(
		'mandatory'           => false,
		'multiple'            => true,
		'tl_class'            => 'w50'
	),
	'sql'                     => "blob NULL"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['internetschach_viewturniere'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['internetschach_viewturniere'],
	'inputType'               => 'checkbox',
	'filter'                  => true,
	'eval'                    => array('tl_class' => 'w50 clr','isBoolean' => true),
	'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['internetschach_viewgruppen'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['internetschach_viewgruppen'],
	'inputType'               => 'checkbox',
	'filter'                  => true,
	'eval'                    => array('tl_class' => 'w50','isBoolean' => true),
	'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['internetschach_tabelle'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['internetschach_tabelle'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('tl_content_internetschach', 'getTabellen'),
	'eval'                    => array
	(
		'multiple'            => false,
		'includeBlankOption'  => true,
		'blankOptionLabel'    => $GLOBALS['TL_LANG']['tl_content']['internetschach_blanklabel']
	),
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['internetschach_spalten'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['internetschach_spalten'],
	'exclude'                 => true,
	'inputType'               => 'checkboxWizard',
	'options'                 => array('platz', 'cb-name', 'cb-land', 'cb-rating', 'punkte', 'wertung1', 'wertung2', 'runden', 'name', 'dwz', 'verein', 'verein_kurz', 'fide-titel', 'fide-elo', 'email', 'titel+name', 'qualification', 'prices'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_content']['internetschach_spalten_reference'],
	'eval'                    => array
	(
		'mandatory'           => false,
		'multiple'            => true,
		'tl_class'            => 'w50'
	),
	'sql'                     => "text NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['internetschach_css'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['internetschach_css'],
	'inputType'               => 'checkbox',
	'filter'                  => true,
	'default'                 => 1,
	'eval'                    => array
	(
		'tl_class'            => 'w50',
		'isBoolean'           => true
	),
	'sql'                     => "char(1) NOT NULL default ''"
);

// Anzahl der Topplätze
$GLOBALS['TL_DCA']['tl_content']['fields']['internetschach_topanzahl'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['internetschach_topanzahl'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'default'                 => 10,
	'eval'                    => array
	(
		'tl_class'            => 'w50',
		'mandatory'           => false,
		'maxlength'           => 4,
		'rgxp'                => 'numeric'
	),
	'sql'                     => "int(4) unsigned NOT NULL default '10'"
);

// Anzahl der bewerteten Plätze
$GLOBALS['TL_DCA']['tl_content']['fields']['internetschach_punktplaetze'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['internetschach_punktplaetze'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'default'                 => 50,
	'eval'                    => array
	(
		'tl_class'            => 'w50',
		'mandatory'           => false,
		'maxlength'           => 4,
		'rgxp'                => 'numeric'
	),
	'sql'                     => "int(4) unsigned NOT NULL default '50'"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['internetschach_plaetze'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['internetschach_plaetze'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array
	(
		'tl_class'            => 'w50',
		'mandatory'           => false,
		'maxlength'           => 4,
		'rgxp'                => 'numeric'
	),
	'sql'                     => "smallint(4) unsigned NOT NULL default '0'"
);


/*****************************************
 * Klasse tl_content_internetschach
 *****************************************/

class tl_content_internetschach extends \Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}

	public function getTurnierserie(DataContainer $dc)
	{
		$array = array();
		$objTurnier = $this->Database->prepare("SELECT * FROM tl_internetschach ORDER BY titel ASC")->execute();
		while($objTurnier->next())
		{
			$array[$objTurnier->id] = $objTurnier->titel;
		}
		return $array;
	}

	public function getTurniere(DataContainer $dc)
	{
		$array = array();
		//print_r($dc->activeRecord);
		$objTurniere = $this->Database->prepare("SELECT turniere FROM tl_internetschach WHERE id = ?")->execute($dc->activeRecord->internetschach);
		if($objTurniere->numRows)
		{
			$temp = unserialize($objTurniere->turniere);
			if($temp)
			{
				foreach($temp as $item)
				{
					$array[$item['feldname']] = $item['name'];
				}
			}
		}
		return $array;
	}

	public function getGruppen(DataContainer $dc)
	{
		$array = array();
		$objTurniere = $this->Database->prepare("SELECT gruppen FROM tl_internetschach WHERE id = ?")->execute($dc->activeRecord->internetschach);
		if($objTurniere->numRows)
		{
			$temp = unserialize($objTurniere->gruppen);
			if($temp)
			{
				foreach($temp as $item)
				{
					$array[$item['feldname']] = $item['name'];
				}
			}
		}
		return $array;
	}

	public function getTabellen(DataContainer $dc)
	{
		$array = array();
		if($dc->activeRecord->internetschach)
		{
			$objTabellen = \Database::getInstance()->prepare("SELECT * FROM tl_internetschach_tabellen WHERE pid = ? ORDER BY turnier ASC, gruppe ASC")
			                                       ->execute($dc->activeRecord->internetschach);

			if($objTabellen->numRows)
			{
				while($objTabellen->next())
				{
				$array[$objTabellen->id] = \Schachbulle\ContaoInternetschachBundle\Classes\Helper::getTurnier($dc->activeRecord->internetschach, $objTabellen->turnier).' - '.\Schachbulle\ContaoInternetschachBundle\Classes\Helper::getGruppe($dc->activeRecord->internetschach, $objTabellen->gruppe);
				}
			}
		}
		return $array;
	}

}
