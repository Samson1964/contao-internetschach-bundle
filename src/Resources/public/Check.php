<?php
ini_set('display_errors', '1');

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
define('TL_SCRIPT', 'bundles/contaointernetschach/Check.php');
require($_SERVER['DOCUMENT_ROOT'].'/../system/initialize.php');

/**
 * Class Check
 *
 */
class Check
{
	public function __construct()
	{
	}

	public function run()
	{
		$playerid = \Input::get('playerid');
		$cbname = \Input::get('cbname');

		if($cbname)
		{
			$response = file_get_contents('https://play.chessbase.com/de/info?account='.rawurlencode($cbname));
			$chessbase = json_decode($response);
			if($chessbase->success)
			{
				// Benutzername vorhanden
				echo '<span style="color:green; font-weight:bold;">ChessBase-Benutzername gefunden!</span>';
				if(!$chessbase->last && !$chessbase->first)
				{
					// Spielername in ChessBase fehlt
					echo ' <span style="color:red; font-weight:bold;">Realer Name fehlt im ChessBase-Konto!</span>';
				}
				
				// Spielername gegenprüfen
				if($playerid)
				{
					// Spieler laden
					$player = \Database::getInstance()->prepare("SELECT * FROM tl_internetschach_spieler WHERE id = ?")
					                                  ->execute($playerid);
					if($player->numRows)
					{
						$cbkontoname = $chessbase->last.','.$chessbase->first;
						if($player->name == $cbkontoname)
						{
							echo ' <span style="color:green; font-weight:bold;">Realer Name im ChessBase-Konto okay.</span>';
						}
						else
						{
							echo ' <span style="color:red; font-weight:bold;">Realer Name im ChessBase-Konto weicht ab: '.$cbkontoname.'</span>';
						}
					}
				}
				else
				{
					// Es wurde noch kein Spieler ausgewählt
					echo ' <span style="color:red; font-weight:bold;">Kein Spieler im Formular ausgewählt!</span>';
				}
			}
			else
			{
				echo '<span style="color:red; font-weight:bold;">ChessBase-Benutzername nicht gefunden!</span>';
			}
			
		
		}
		else
		{
			echo '';
		}

	}
}

/**
 * Instantiate controller
 */
$objClick = new Check();
$objClick->run();
