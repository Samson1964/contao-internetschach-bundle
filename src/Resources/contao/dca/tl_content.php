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
$GLOBALS['TL_DCA']['tl_content']['palettes']['internetschach'] = '{type_legend},type,headline;{internetschach_legend},internetschach;{protected_legend:hide},protected;{expert_legend:hide},guest,cssID,space;{invisible_legend:hide},invisible,start,stop';

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['internetschach'] = array
(
	'label'                => &$GLOBALS['TL_LANG']['tl_content']['internetschach'],
	'exclude'              => true,
	'options_callback'     => array('tl_content_internetschach', 'getTurnierliste'),
	'inputType'            => 'select',
	'eval'                 => array
	(
		'mandatory'      => false,
		'multiple'       => false,
		'chosen'         => true,
		'submitOnChange' => false,
		'tl_class'       => 'long'
	),
	'sql'                  => "int(10) unsigned NOT NULL default '0'"
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

	public function getTurnierliste(DataContainer $dc)
	{
		$array = array();
		$objTurnier = $this->Database->prepare("SELECT * FROM tl_internetschach ORDER BY titel ASC")->execute();
		while($objTurnier->next())
		{
			$array[$objTurnier->id] = $objTurnier->titel;
		}
		return $array;

	}

}
