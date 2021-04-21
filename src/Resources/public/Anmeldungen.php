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
		$format = \Input::get('format'); // Format der Ausgabe: json (default), excel

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
					$export = false;
					if($turnier)
					{
						if(in_array($turnier, $arrTurniere))
						{
							$export = true;
						}
					}
					else
					{
						$export = true;
					}

					if($export)
					{
						//print_r($arrTurniere);
						$ausgabe[] = array
						(
							'gruppe'    => $objAnmeldungen->gruppe,
							'turniere'  => $arrTurniere,
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
		if($format == 'excel')
		{
			// Format excel
			\Schachbulle\ContaoInternetschachBundle\Classes\Helper::exportAnmeldungenToExcel($turnierserie, $ausgabe, $turnier);
		}
		else
		{
			// Format json
			header('Content-Type: application/json');
			echo json_encode($ausgabe);
		}
	}
}

/**
 * Instantiate controller
 */
$objClick = new Anmeldungen();
$objClick->run();
