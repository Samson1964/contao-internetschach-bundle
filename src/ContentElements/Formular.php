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
		global $objPage;

		// Javascript generieren
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
          $(".chosen-search input").val(request.term);
        }
      });
    }
  });
</script>
';

		$content = '';
		$form = new \Schachbulle\ContaoHelperBundle\Classes\Form();
		$form->addField(array
		(
			'typ'       => 'hidden',
			'name'      => 'FORM_SUBMIT',
			'value'     => 'form_internetschach'
		));
		$form->addField(array
		(
			'typ'       => 'hidden',
			'name'      => 'REQUEST_TOKEN',
			'value'     => REQUEST_TOKEN
		));
		$form->addField(array
		(
			'typ'       => 'hidden',
			'name'      => 'pid',
			'value'     => $this->internetschach
		));
		$form->addField(array
		(
			'typ'       => 'select',
			'name'      => 'name',
			'label'     => 'Spieler suchen und wählen',
			'class'     => 'select-box',
			'options'   => array('0' => 'Name,Vorname oder Teil davon eintippen ...'),
			'mandatory' => true
		));
		$form->addField(array
		(
			'typ'       => 'text',
			'name'      => 'email',
			'label'     => 'E-Mail-Adresse',
			'mandatory' => true
		));
		$form->addField(array
		(
			'typ'       => 'text',
			'name'      => 'chessbase',
			'label'     => 'ChessBase-Benutzername',
			'mandatory' => true
		));
		$form->addField(array
		(
			'typ'       => 'explanation',
			'label'     => 'Se können mehrere Benutzernamen mit Komma trennen.'
		));
		$turniere = array();
		$objMain = \Database::getInstance()->prepare('SELECT * FROM tl_internetschach WHERE id = ?')
			                               ->execute($this->internetschach);
		if($objMain->numRows)
		{
			$temp = unserialize($objMain->turniere);
			foreach($temp as $item)
			{
				if(!$item['finale']) $turniere[$item['feldname']] = $item['name'];
			}
		}
		$form->addField(array
		(
			'typ'      => 'checkbox',
			'name'     => 'turniere',
			'label'    => 'Turniere',
			'options'  => $turniere
		));
		$form->addField(array
		(
			'typ'      => 'textarea',
			'name'     => 'bemerkungen',
			'label'    => 'Bemerkungen',
			'rows'     => 10,
			'cols'     => 40
		));
		$form->addField(array
		(
			'typ'      => 'submit',
			'label'    => 'Anmeldung abschicken'
		));
		$content = $form->generate();

		// Template ausgeben
		$this->Template = new \FrontendTemplate($this->strTemplate);
		$this->Template->class = "ce_internetschach";

		if($form->validate())
		{
			$arrData = $form->fetchAll();
			self::saveAnmeldung($arrData); // Daten sichern
			// Seite neu laden
			\Controller::addToUrl('send=1'); // Hat keine Auswirkung, verhindert aber das das Formular ausgefüllt ist
			//\Controller::reload();
			header('Location:'.$objPage->alias.'.html?send=1'); 
		}
		else
		{
			if(\Input::get('send'))
			{
				$this->Template->content = 'Vielen Dank für Ihre Anmeldung!';
			}
			else
			{
				$this->Template->content = $content.$javascript;
			}
		}

		return;

	}

	function saveAnmeldung($arrData)
	{
		//echo "Turniere:";
		//echo \Input::post('turniere')."<br>";
		//echo "Formulrdaten:";
		//print_r($arrData);
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
			'bemerkungen' => $arrData['bemerkungen'],
			'playerId'    => $arrData['name'],
			'verein'      => $spieler['verein'],
			'name'        => $spieler['name'],
			'geschlecht'  => $spieler['geschlecht'],
			'geburtsjahr' => $spieler['geburtsjahr'],
			'dwz'         => $spieler['dwz'],
			'fideElo'     => $spieler['fideElo'],
			'fideTitel'   => $spieler['fideTitel'],
			'turniere'    => serialize($arrData['turniere']),
			'published'   => 1
		);
		//print_r($set);
		$objLink = \Database::getInstance()->prepare('INSERT INTO tl_internetschach_anmeldungen %s')
		                                   ->set($set)
		                                   ->execute();

		\System::log('[Internetschach] Neue Anmeldung: '.$set['name'], __CLASS__.'::'.__FUNCTION__, TL_CRON);

		$objMain = \Database::getInstance()->prepare('SELECT * FROM tl_internetschach WHERE id = ?')
		                                   ->execute($arrData['pid']);

		if($objMain->numRows)
		{
			// Email an Spieler verschicken
			$objEmail = new \Email();
			// E-Mail-Adressen umwandeln
			$from = html_entity_decode($objMain->email_sender);
			$replyto = html_entity_decode($objMain->email_replyto);
			$to = html_entity_decode($objMain->email_to);

			// Absender "Name <email>" in ein Array $arrFrom aufteilen
			preg_match('~(?:([^<]*?)\s*)?<(.*)>~', $from, $arrFrom);
			
			$objEmail->from = $arrFrom[2];
			$objEmail->fromName = $arrFrom[1];
			$objEmail->subject = $objMain->titel.' - Anmeldung '.$set['name'];

			// Text zusammenbauen
			$text = 'Sie haben sich für '.$objMain->titel." angemeldet. Folgende Daten wurden an uns übertragen:\n\n";
			foreach($set as $key => $value)
			{
				switch($key)
				{
					case 'tstamp':
						$text .= 'Anmeldezeit: '.date('d.m.Y H:i', $value)."\n";
						break;
					case 'email':
						$text .= 'E-Mail: '.$value."\n";
						break;
					case 'chessbase':
						$text .= 'ChessBase-Benutzername(n): '.$value."\n";
						break;
					case 'bemerkungen':
						$text .= 'Bemerkungen: '.$value."\n";
						break;
					case 'verein':
						$text .= 'Verein: '.$value."\n";
						break;
					case 'name':
						$text .= 'Name: '.$value."\n";
						break;
					case 'dwz':
						$text .= 'DWZ: '.$value."\n";
						break;
					case 'fideElo':
						$text .= 'FIDE-Elo: '.$value."\n";
						break;
					case 'fideTitel':
						$text .= 'FIDE-Titel: '.$value."\n";
						break;
					case 'turniere':
						$temp = (array)unserialize($value);
						$text .= 'Gemeldete Turniere: '.implode(',', $temp)."\n";
					default:
				}
			}
			$text .= "\nVielen Dank für Ihre Anmeldung!\nSie stehen unter Vorbehalt der Prüfung bereits auf der Meldeliste.\n\nIhr Deutscher Schachbund";
			$objEmail->text = $text;

			$objEmail->sendBcc($to);
			$objEmail->sendTo($set['email']);
		}
	}
}
