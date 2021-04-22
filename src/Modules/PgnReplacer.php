<?php

/**
 * Contao Open Source CMS
 *
 * @package   PgnReplacer
 * @author    Frank Hoppe
 * @license   GNU/LGPL
 * @copyright Frank Hoppe 2021
 */

namespace Schachbulle\ContaoInternetschachBundle\Modules;

class PgnReplacer extends \Module
{

	protected $strTemplate = 'mod_internetschach';

	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### INTERNETSCHACH PGN-REPLACER ###';
			$objTemplate->title = $this->name;
			$objTemplate->id = $this->id;

			return $objTemplate->parse();
		}

		return parent::generate(); // Weitermachen mit dem Modul
	}

	/**
	 * Generate the module
	 */
	protected function compile()
	{

		// Der 1. Parameter ist die Formular-ID (hier "linkform")
		// Der 2. Parameter ist GET oder POST
		// Der 3. Parameter ist eine Funktion, die entscheidet wann das Formular gesendet wird (Third is a callable that decides when your form is submitted)
		// Der optionale 4. Parameter legt fest, ob das ausgegebene Formular auf Tabellen basiert (true)
		// oder nicht (false) (You can pass an optional fourth parameter (true by default) to turn the form into a table based one)
		$objForm = new \Haste\Form\Form('pgnform', 'POST', function($objHaste)
		{
			return \Input::post('FORM_SUBMIT') === $objHaste->getFormId();
		});

		// URL für action festlegen. Standard ist die Seite auf der das Formular eingebunden ist.
		// $objForm->setFormActionFromUri();

		$objForm->addFormField('pgnfile', array
		(
			'label'         => 'PGN-Datei',
			'inputType'     => 'upload',
			'eval'          => array
			(
				'mandatory'        => true,
				'maxlength'        => \Config::get('maxFileSize'),
				'fSize'            => \Config::get('maxFileSize'),
				'extensions'       => 'pgn',
				//'uploadFolder'     => 'bundles/contaointernetschachbundle',
				//'doNotOverwrite' => '1', // Datei nicht überschreiben (Versionsnummer anhängen)
				//'storeFile'        => '1', // Datei speichern
			)
		));
		$objForm->addFormField('turnierserie', array(
			'label'         => 'Ersetzungen mit Anmeldungen von',
			'inputType'     => 'select',
			'options'       => \Schachbulle\ContaoInternetschachBundle\Classes\Helper::getTurnierserien(),
			'eval'          => array('mandatory'=>true, 'choosen'=>true, 'class'=>'form-control')
		));
		// Submit-Button hinzufügen
		$objForm->addFormField('submit', array(
			'label'         => 'Verarbeitung starten',
			'inputType'     => 'submit',
			'eval'          => array('class'=>'btn btn-primary')
		));

		// validate() prüft auch, ob das Formular gesendet wurde
		if($objForm->validate())
		{
			// Alle gesendeten und analysierten Daten holen (funktioniert nur mit POST)
			$arrData = $objForm->fetchAll();
			self::getReplacer($arrData); // Daten verarbeiten
			// Seite neu laden
			//\Controller::addToUrl('send=1'); // Hat keine Auswirkung, verhindert aber das das Formular ausgefüllt ist
			//\Controller::reload();
		}

		// Formular als String zurückgeben
		$this->Template->content = $objForm->generate();

	}

	protected function getReplacer($data)
	{
		echo "<pre>";
		print_r($_SESSION['FILES']['pgnfile']);
		print_r($data);
		echo "</pre>";

		// Array $_SESSION['FILES']['pgnfile']
		// (
		//     [name] => lichess_tournament_2021.04.14_VOFxCHXm_mittwochs-blitz-32.pgn
		//     [type] => application/octet-stream
		//     [tmp_name] => /temp/107305/u107305/development.schachbund.de/phpnduHxq
		//     [error] => 0
		//     [size] => 39476
		// )
		$pgnfile = $_SESSION['FILES']['pgnfile'];

		$fp = fopen($pgnfile['tmp_name'], 'r');

		while(!feof($fp))
		{
			$line = fgets($fp);
			if(substr($line, 0, 7) == '[White ')
			{
				$pgn_white = true;
			}
			if(substr($line, 0, 7) == '[Black ')
			{
				$pgn_black = true;
			}
			if(substr($line, 0, 10) == '[WhiteElo ')
			{
				$pgn_welo = true;
			}
			if(substr($line, 0, 10) == '[BlackElo ')
			{
				$pgn_belo = true;
			}
			echo $line."<br>";
		}
		fclose($fp);


		\System::log('[Linkscollection] New Link submitted: '.$data['title'].' ('.$data['url'].')', __CLASS__.'::'.__FUNCTION__, TL_CRON);

	}

}
