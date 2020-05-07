<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * @package   chesstable
 * Version    1.0.0
 * @author    Frank Hoppe
 * @license   GNU/LGPL
 * @copyright Frank Hoppe 2013
 */

namespace Schachbulle\ContaoInternetschachBundle\ContentElements;

class Formular extends \ContentElement
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'ce_internetschach';

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
		$objForm = new \Haste\Form\Form('internetschachform', 'POST', function($objHaste)
		{
			return \Input::post('FORM_SUBMIT') === $objHaste->getFormId();
		});
		
		// URL für action festlegen. Standard ist die Seite auf der das Formular eingebunden ist.
		// $objForm->setFormActionFromUri();
	
		$elemente = array
		(
			'1' => 'eins',
			'2' => 'zwei',
			'3' => 'drei',
			'4' => 'vier',
		);
			
		$objForm->addFormField('pid', array(
			'inputType'     => 'hidden',
			'default'       => $this->internetschach
		));
		$objForm->addFormField('name', array(
			'label'         => 'Spieler suchen und wählen',
			'inputType'     => 'select',
			//'selected'      => array('1'),
			//'options'       => array_keys($elemente),
			//'reference'     => $elemente,
			'eval'          => array('mandatory'=>true, 'class'=>'form-control select-box')
		));
		$objForm->addFormField('email', array(
			'label'         => 'E-Mail',
			'inputType'     => 'text',
			'eval'          => array('mandatory'=>true, 'rgxp'=>'email', 'class'=>'form-control')
		));
		$objForm->addFormField('chessbase', array(
			'label'         => 'ChessBase-Benutzername',
			'inputType'     => 'text',
			'eval'          => array('mandatory'=>true, 'class'=>'form-control')
		));
		$objForm->addFormField('description', array(
			'label'         => 'Bemerkungen',
			'inputType'     => 'textarea',
			'eval'          => array('mandatory'=>false, 'rte'=>'tinyMCE', 'class'=>'form-control')
		));
		// Submit-Button hinzufügen
		$objForm->addFormField('submit', array(
			'label'         => 'Anmeldung abschicken',
			'inputType'     => 'submit',
			'eval'          => array('class'=>'btn btn-primary')
		));
		$objForm->addCaptchaFormField('captcha');

		// Ausgeblendete Felder FORM_SUBMIT und REQUEST_TOKEN automatisch hinzufügen.
		// Nicht verwenden wenn generate() anschließend verwendet, da diese Felder dort standardmäßig bereitgestellt werden.
		$objForm->addContaoHiddenFields();
		
		$objForm->setNoValidate('name');
		
		// validate() prüft auch, ob das Formular gesendet wurde
		//if($objForm->isSubmitted())
		if($objForm->validate())
		{
			// Alle gesendeten und analysierten Daten holen (funktioniert nur mit POST)
			$arrData = $objForm->fetchAll();
			self::saveAnmeldung($arrData); // Daten sichern
			// Seite neu laden
			\Controller::addToUrl('send=1'); // Hat keine Auswirkung, verhindert aber das das Formular erneut ausgefüllt ist
			//\Controller::reload(); 
		}

		// Javascript ergänzen
		$javascript ='
<script type="text/javascript">
  $("select.select-box").chosen();
  $(\'.chosen-search input\').autocomplete({
    delay: 500,
    minLength: 2,
    autoFocus: false,
    position: { my : "right top", at: "right bottom" },
    source: function(request, response) {
      $.ajax({
        url: "bundles/contaointernetschach/Spielerliste.php?pid='.$this->internetschach.'&q="+request.term,
        dataType: "json",
        success: function(data) {
          $(\'select.select-box\').empty();
          response($.map(data, function(item) {
            $(\'select.select-box\').append(\'<option value="\'+item.id+\'">\' + item.name + \'</option>\');
          }));
          $("select.select-box").trigger("chosen:updated");
          //$(".chosen-search input").val(request.term);
        }
      });
    }
  });
</script>
';

		// Template ausgeben
		$this->Template = new \FrontendTemplate($this->strTemplate);
		$this->Template->class = "ce_internetschach";
		$this->Template->content = $objForm->generate().$javascript;

		return;

	}

	function saveAnmeldung($arrData)
	{
		print_r($arrData);
		// Spielerdaten laden
		if($arrData['name'])
		{
			$objPlayer = \Database::getInstance()->prepare('SELECT * FROM tl_internetschach_spieler WHERE id = ?')
			                                     ->execute($arrData['name']);
			if($objPlayer->numRows)
			{
				$spieler = array
				(
					'verein'      => $objPlayer->verein,
					'name'        => $objPlayer->name,
					'geschlecht'  => $objPlayer->geschlecht,
					'geburtsjahr' => $objPlayer->geburtsjahr,
					'dwz'         => $objPlayer->dwz,
					'fideElo'     => $objPlayer->fideElo,
					'fideTitel'   => $objPlayer->fideTitel
				);
			}
		}
		if(!$spieler)
		{
			$spieler = array
			(
				'verein'      => '',
				'name'        => '',
				'geschlecht'  => '',
				'geburtsjahr' => '',
				'dwz'         => '',
				'fideElo'     => '',
				'fideTitel'   => ''
			);
		}

		// Formulardaten übertragen
		$set = array
		(
			'pid'         => $arrData['pid'],
			'tstamp'      => time(),
			'email'       => $arrData['email'],
			'chessbase'   => $arrData['chessbase'],
			'bemerkungen' => $arrData['description'],
			'playerId'    => $arrData['name'],
			'verein'      => $spieler['verein'],
			'name'        => $spieler['name'],
			'geschlecht'  => $spieler['geschlecht'],
			'geburtsjahr' => $spieler['geburtsjahr'],
			'dwz'         => $spieler['dwz'],
			'fideElo'     => $spieler['fideElo'],
			'fideTitel'   => $spieler['fideTitel'],
			'published'   => 1
		);
		$objLink = \Database::getInstance()->prepare('INSERT INTO tl_internetschach_anmeldungen %s')
		                                   ->set($set)
		                                   ->execute();

		\System::log('[Internetschach] Neue Anmeldung: '.$set['name'], __CLASS__.'::'.__FUNCTION__, TL_CRON);

		// Email an Spieler verschicken
		$objEmail = new \Email();
		$objEmail->from = $GLOBALS['TL_ADMIN_EMAIL'];
		$objEmail->fromName = $GLOBALS['TL_ADMIN_NAME'];
		$objEmail->subject = 'Internetschach - neue Anmeldung';

		// Kommentar zusammenbauen

		$objEmail->sendTo(array($GLOBALS['TL_ADMIN_NAME'].' <'.$GLOBALS['TL_ADMIN_EMAIL'].'>'));
	}
}
