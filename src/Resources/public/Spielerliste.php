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
		$startzeit = microtime(true); // Startzeit

		$turnierserie = \Input::get('pid');
		$search = str_replace(', ', ',', \Input::get('q')); // Leerzeichen nach Komma entfernen

		// Turnierserie laden
		$objSerie = \Database::getInstance()->prepare("SELECT * FROM tl_internetschach WHERE id = ?")
		                                    ->execute($turnierserie);

		// Höchste zulässige DWZ suchen, wenn es DWZ-Gruppen gibt
		$gruppen = unserialize($objSerie->gruppen);
		$dwz_max = 0;
		if(!empty($gruppen[0]['name']))
		{
			// Es gibt DWZ-Gruppen
			foreach($gruppen as $item)
			{
				if($item['dwz_bis'] > $dwz_max) $dwz_max = $item['dwz_bis'];
			}
		}
		else
		{
			$dwz_max = 4000; // Maximum-DWZ hochsetzen, da keine Gruppen definiert sind
		}

		$ausgabeArr = array();
		if($turnierserie && $search)
		{
			if(strlen($search) > 1)
			{

				// Cache initialisieren
				$cache = new \Schachbulle\ContaoHelperBundle\Classes\Cache('Internetschach');
				$cache->eraseExpired(); // Cache aufräumen, abgelaufene Schlüssel löschen
				$cachekey = strtolower($search);

				if($cache->isCached($cachekey))
				{
					// Daten aus dem Cache laden
					$ausgabeArr = $cache->retrieve($cachekey);
				}
				else
				{
					$player = \Database::getInstance()->prepare("SELECT * FROM tl_internetschach_spieler WHERE published = ? AND pid = ? AND status = ? AND name LIKE ? AND dwz <= ? ORDER BY name ASC")
					                                  ->execute(1, $turnierserie, 'A', "%$search%", $dwz_max);
					// Suchbegriff als Erstes zurückgeben
					//$ausgabeArr[] = array
					//(
					//	'id'   => 0,
					//	'text' => $search
					//);
					if($player->numRows)
					{
						while($player->next())
						{
							$ausgabeArr[] = array
							(
								'id'   => $player->id,
								'text' => $player->name.' ('.($player->dwz ? 'DWZ '.$player->dwz : 'ohne DWZ').', '.$player->verein.') - '.\Schachbulle\ContaoInternetschachBundle\Classes\Helper::Gruppenzuordnung($turnierserie, $player->dwz)
							);
						}
						// Daten im Cache speichern
						$cachetime = 3600 * 48; // 48 Stunden
						//$cachetime = 0 * 48; // 48 Stunden
						$cache->store($cachekey, $ausgabeArr, $cachetime);
					}
				}
			}
			else
			{
				// Suchstring zu kurz
				//$ausgabeArr[] = array
				//(
				//	'id'   => 0,
				//	'text' => $search
				//);
			}
		}

		$stopzeit = microtime(true); // Stopzeit
		// Auswertung
		$laufzeit = ($stopzeit-$startzeit); // Berechnung
		$laufzeit = substr($laufzeit, 0, 7); // Auf 5 Stellen begrenzen
		//echo "Scriptlaufzeit: ".$laufzeit." Sekunden<br>"; // Ausgabe
		echo json_encode(array('results' => $ausgabeArr));

	}
}

/**
 * Instantiate controller
 */
$objClick = new Spielerliste();
$objClick->run();
