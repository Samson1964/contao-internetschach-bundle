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

			$ergebnis = \Schachbulle\ContaoInternetschachBundle\Classes\Helper::ChessbaseCheck($cbname, $playerid);

			if($ergebnis['error'])
			{
				echo '<span style="color:red; font-weight:bold;">'.$ergebnis['text'].'</span>';
			}
			else
			{
				echo '<span style="color:green; font-weight:bold;">'.$ergebnis['text'].'</span>';
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
