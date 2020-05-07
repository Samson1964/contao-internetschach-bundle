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
		$search = \Input::get('q');

		$ausgabeArr = array();
		if($turnierserie && $search)
		{
			if(strlen($search) > 1)
			{
				$player = \Database::getInstance()->prepare("SELECT * FROM tl_internetschach_spieler WHERE published = ? AND pid = ? AND status = ? AND name LIKE ?")
				                                  ->execute(1, $turnierserie, 'A', "%$search%");

				// Suchstring zu kurz
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
							'name' => $player->name.' ('.$player->verein.')'
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
