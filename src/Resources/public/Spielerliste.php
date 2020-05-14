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
define('TL_SCRIPT', 'bundles/contaointernetschach/Spielerliste.php');
require($_SERVER['DOCUMENT_ROOT'].'/../system/initialize.php');

/**
 * Class LinkSearch
 *
 */
class Spielerliste
{
	public function __construct()
	{
	}

	public function run()
	{
		$turnierserie = \Input::get('pid');
		$search = str_replace(', ', ',', \Input::get('q')); // Leerzeichen nach Komma entfernen

		// Turnierserie laden
		$objSerie = \Database::getInstance()->prepare("SELECT * FROM tl_internetschach WHERE id = ?")
		                                    ->execute($turnierserie);

		// Höchste zulässige DWZ suchen
		$daten = unserialize($objSerie->turniere);
		$dwz = 0;
		foreach($daten as $item)
		{
			if($item['dwz_bis'] > $dwz) $dwz = $item['dwz_bis'];
		}

		$ausgabeArr = array();
		if($turnierserie && $search)
		{
			if(strlen($search) > 1)
			{
				$player = \Database::getInstance()->prepare("SELECT * FROM tl_internetschach_spieler WHERE published = ? AND pid = ? AND status = ? AND name LIKE ? AND dwz <= ? ORDER BY name ASC")
				                                  ->execute(1, $turnierserie, 'A', "%$search%", $dwz);
				// Suchbegriff als Erstes zurückgeben
				$ausgabeArr[] = array
				(
					'id'   => 0,
					'name' => $search
				);
				if($player->numRows)
				{
					while($player->next())
					{
						$ausgabeArr[] = array
						(
							'id'   => $player->id,
							'name' => $player->name.' ('.($player->dwz ? 'DWZ '.$player->dwz : 'ohne DWZ').', '.$player->verein.') - '.\Schachbulle\ContaoInternetschachBundle\Classes\Helper::Gruppenzuordnung($turnierserie, $player->dwz)
						);
					}
				}
			}
			else
			{
				// Suchstring zu kurz
				$ausgabeArr[] = array
				(
					'id'   => 0,
					'name' => $search
				);
			}
		}

		echo json_encode($ausgabeArr);
	}
}

/**
 * Instantiate controller
 */
$objClick = new Spielerliste();
$objClick->run();
