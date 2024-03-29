<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @package   Elo
 * @author    Frank Hoppe
 * @license   GNU/LPGL
 * @copyright Frank Hoppe 2016
 */


/**
 * BACK END MODULES
 *
 * Back end modules are stored in a global array called "BE_MOD". You can add
 * your own modules by adding them to the array.
 *
 * Not all of the keys mentioned above (like "tables", "key", "callback" etc.)
 * have to be set. Take a look at the system/modules/core/config/config.php
 * file to see how back end modules are configured.
 */

/**
 * -------------------------------------------------------------------------
 * BACKEND-MODULE
 * -------------------------------------------------------------------------
 */
$GLOBALS['BE_MOD']['content']['internetschach'] = array
(
	'tables'               => array('tl_internetschach', 'tl_internetschach_spieler', 'tl_internetschach_anmeldungen', 'tl_internetschach_tabellen', 'tl_internetschach_preise'),
	'importCSV'            => array('\Schachbulle\ContaoInternetschachBundle\Classes\Import', 'importCSV'),
	'importTable'          => array('\Schachbulle\ContaoInternetschachBundle\Classes\Import', 'importTable'),
	'exportXLS'            => array('\Schachbulle\ContaoInternetschachBundle\Classes\Export', 'getExcel'),
	'qualifikationen'      => array('\Schachbulle\ContaoInternetschachBundle\Classes\Qualifikationen', 'Aktualisieren'),
	'finalqualifikationen' => array('\Schachbulle\ContaoInternetschachBundle\Classes\Qualifikationen', 'FinaleAktualisieren'),
	'preise'               => array('\Schachbulle\ContaoInternetschachBundle\Classes\Preise', 'Aktualisieren'),
	'exportPreiseXLS'      => array('\Schachbulle\ContaoInternetschachBundle\Classes\ExportPreise', 'getExcel'),
	'copyToAnmeldung'      => array('\Schachbulle\ContaoInternetschachBundle\Classes\Export', 'copyToAnmeldung')
);

/**
 * -------------------------------------------------------------------------
 * FRONTEND-MODULE
 * -------------------------------------------------------------------------
 */
$GLOBALS['FE_MOD']['schach']['internetschach_pgnreplacer'] = 'Schachbulle\ContaoInternetschachBundle\Modules\PgnReplacer';


/**
 * -------------------------------------------------------------------------
 * CONTENT-ELEMENTE
 * -------------------------------------------------------------------------
 */
$GLOBALS['TL_CTE']['includes']['internetschach_formular'] = 'Schachbulle\ContaoInternetschachBundle\ContentElements\Formular';
$GLOBALS['TL_CTE']['includes']['internetschach_anmeldungen'] = 'Schachbulle\ContaoInternetschachBundle\ContentElements\Anmeldungen';
$GLOBALS['TL_CTE']['includes']['internetschach_tabelle'] = 'Schachbulle\ContaoInternetschachBundle\ContentElements\Tabelle';
$GLOBALS['TL_CTE']['includes']['internetschach_topspieler'] = 'Schachbulle\ContaoInternetschachBundle\ContentElements\Topspieler';

if(TL_MODE == 'BE')
{
	$GLOBALS['TL_CSS'][] = 'bundles/contaointernetschach/css/be.css';
}
