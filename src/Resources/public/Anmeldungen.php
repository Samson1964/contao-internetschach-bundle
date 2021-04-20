<?php
ini_set('display_errors', '1');
set_time_limit(0);

/**
 * Contao Open Source CMS, Copyright (C) 2005-2013 Leo Feyer
 *
 */

/**
 * Run in a custom namespace, so the class can be replaced
 */
use Contao\Controller;

/**
 * Initialize the system
 */
define('TL_MODE', 'FE');
define('TL_SCRIPT', 'bundles/contaointernetschach/Anmeldungen.php');
require($_SERVER['DOCUMENT_ROOT'].'/../system/initialize.php');

/**
 * Class Anmeldungen
 *
 */
class Anmeldungen
{
	public function __construct()
	{
	}

	public function run()
	{
		$turnierserie = \Input::get('id'); // ID der Turnierserie = tl_internetschach_anmeldungen.pid
		$turnier = \Input::get('turnier'); // Kurzzeichen des Turniers = tl_internetschach_anmeldungen.turniere

		// Veröffentlichte Anmeldungen der Turnierserie laden
		$objAnmeldungen = \Database::getInstance()->prepare("SELECT * FROM tl_internetschach_anmeldungen WHERE pid = ? AND published = ?")
		                                          ->execute($turnierserie, 1);

		$ausgabe = array();
		if($objAnmeldungen->numRows)
		{
			while($objAnmeldungen->next())
			{
				$arrTurniere = unserialize($objAnmeldungen->turniere);
				if($arrTurniere)
				{
					if(in_array($turnier, $arrTurniere))
					{
						//print_r($arrTurniere);
						$ausgabe[] = array
						(
							'gruppe'    => '',
							'turniere'  => implode(',', $arrTurniere),
							'name'      => $objAnmeldungen->name,
							'verein'    => $objAnmeldungen->verein,
							'dwz'       => $objAnmeldungen->dwz,
							'titel'     => $objAnmeldungen->fideTitel,
							'chessbase' => $objAnmeldungen->chessbase,
							'finale'    => ''
						);
					}
				}
			}
		}

		//echo "<pre>";
		//print_r($ausgabe);
		//echo "</pre>";
		header('Content-Type: application/json');
		echo json_encode($ausgabe);

	}
}

/**
 * Instantiate controller
 */
$objClick = new Anmeldungen();
$objClick->run();
