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
		// Turnierserie einlesen
		$objMain = \Database::getInstance()->prepare('SELECT * FROM tl_internetschach WHERE id = ?')
			                               ->execute($this->internetschach);

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
			'typ'       => 'explanation',
			'label'     => '<span style="color:red; font-weight:bold;">* Pflichtfeld!</span>'
		));
		$form->addField(array
		(
			'typ'       => 'select',
			'name'      => 'name',
			'label'     => 'Spieler suchen und wählen',
			'class'     => 'select-box',
			'options'   => array('0' => 'Name,Vorname oder Teil davon eintippen bis die Autovervollständigung aktiv wird'),
			'mandatory' => true
		));
		$form->addField(array
		(
			'typ'       => 'explanation',
			'label'     => 'Es werden nur teilnahmeberechtigte Spieler angezeigt.'
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
			'label'     => 'Sie können mehrere Benutzernamen mit Komma trennen.'
		));
		$turniere = array();
		if($objMain->numRows)
		{
			$temp = unserialize($objMain->turniere);
			foreach($temp as $item)
			{
				// Prüfen ob Anmeldeschluß eingehalten wird
				if(!$item['meldeschluss'] || $item['meldeschluss'] > time())
				{
					if(!$item['finale']) $turniere[$item['feldname']] = $item['name'];
				}
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
			if($arrData['name'])
			{
				self::saveAnmeldung($arrData); // Daten sichern
				// Seite neu laden
				//$this->Template->content = $content.$javascript;
				header('Location:'.$objPage->alias.'.html?send=1');
			}
			else
			{
				// Kein Spieler ausgesucht, Formular mit Fehlermeldung anzeigen
				$this->Template->content = $content.$javascript;
			}
		}
		else
		{
			if(\Input::get('send'))
			{
				$this->Template->content = 'Vielen Dank für Ihre Anmeldung!';
			}
			else
			{
				if($turniere) $this->Template->content = $content.$javascript;
				else $this->Template->content = 'Es sind keine Anmeldungen mehr möglich!';
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

		// Spielerdaten laden, wenn ID im Feld name größer 0
		if($arrData['name'])
		{
			// Daten der Turnierserie laden
			$objMain = \Database::getInstance()->prepare('SELECT * FROM tl_internetschach WHERE id = ?')
			                                   ->execute($arrData['pid']);

			// Anmeldung laden, wenn playerId = name (für Prüfung Mehrfachanmeldung)
			$objAnmeldung = \Database::getInstance()->prepare('SELECT * FROM tl_internetschach_anmeldungen WHERE playerId = ?')
			                                        ->limit(1)
			                                        ->execute($arrData['name']);

			// Spielerdaten suchen
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

		// Anmeldung ohne Datensatz in Spielerdaten
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

		// Alle Daten übertragen
		$set = array
		(
			'pid'          => $arrData['pid'],
			'tstamp'       => time(),
			'registerDate' => time(),
			'email'        => $arrData['email'],
			'chessbase'    => $arrData['chessbase'],
			'bemerkungen'  => $arrData['bemerkungen'],
			'playerId'     => $arrData['name'],
			'verein'       => $spieler['verein'],
			'name'         => $spieler['name'],
			'geschlecht'   => $spieler['geschlecht'],
			'geburtsjahr'  => $spieler['geburtsjahr'],
			'dwz'          => $spieler['dwz'],
			'fideElo'      => $spieler['fideElo'],
			'fideTitel'    => $spieler['fideTitel'],
			'turniere'     => serialize($arrData['turniere']),
			'gruppe'       => \Schachbulle\ContaoInternetschachBundle\Classes\Helper::Gruppenzuordnung($arrData['pid'], $spieler['dwz'], true),
			'published'    => 1
		);

		$bemerkungen = $set['bemerkungen']; // Wegen der hinzugefügten Uhrzeit Bemerkungen separat sichern für E-Mail
		
		// Anmeldung speichern
		if($objAnmeldung->numRows)
		{
			// Ältere Anmeldung liegt bereits vor
			
			// ===============================================
			// Überprüfen, ob Turniere gesichert werden müssen
			// ===============================================
			$definierteTurniere = unserialize($objMain->turniere);
			$bishergemeldeteTurniere = unserialize($objAnmeldung->turniere);

			//echo "<pre>Definierte Turniere:\n";
			//print_r(unserialize($objMain->turniere));
			//echo "Bereits gemeldete Turniere:\n";
			//print_r(unserialize($objAnmeldung->turniere));
			//echo "Turniere aus Formular:\n";
			//print_r($arrData['turniere']);

			// Bisher gemeldete Turniere auf Meldeschluß prüfen
			foreach($bishergemeldeteTurniere as $gemeldet)
			{
				foreach($definierteTurniere as $definiert)
				{
					if($gemeldet == $definiert['feldname'])
					{
						// Prüfen ob Anmeldeschluß eingehalten wird
						if($definiert['meldeschluss'] > 0 && $definiert['meldeschluss'] < time())
						{
							// Turnier gefunden, für das der Meldeschluß vorbei ist
							// Turnier in Array vom Formular hinzufügen
							$arrData['turniere'][] = $gemeldet;
						}
					}
				}
			}
			$arrData['turniere'] = array_unique($arrData['turniere']); // Doppelte Einträge löschen


			//echo "Turniere aus Formular (modifiziert):\n";
			//print_r($arrData['turniere']);
			//echo "</pre>";
			//$turniere = array();
			//if($objMain->numRows)
			//{
			//	$temp = unserialize($objMain->turniere);
			//	foreach($temp as $item)
			//	{
			//		// Prüfen ob Anmeldeschluß eingehalten wird
			//		if(!$item['meldeschluss'] || $item['meldeschluss'] > time())
			//		{
			//			if(!$item['finale']) $turniere[$item['feldname']] = $item['name'];
			//		}
			//	}
			//}

			
			// Versionierung aktivieren
			$objVersion = new \Versions('tl_internetschach_anmeldungen', $objAnmeldung->id);
			$objVersion->setUsername('Internetschach-Bundle');
			$objVersion->setUserId(0);
			$objVersion->initialize();
			// set-Array aktualisieren
			$updateSet = array
			(
				'tstamp'       => time(),
				'email'        => $set['email'], // Neu aus Formular übernehmen
				'chessbase'    => $set['chessbase'], // Neu aus Formular übernehmen
				'bemerkungen'  => $objAnmeldung->bemerkungen.($set['bemerkungen'] ? "\n".date('d.m.Y H:i').' Uhr: '.$set['bemerkungen']: ''), // Neue Bemerkungen hinzufügen
				'turniere'     => serialize($arrData['turniere']), // Neu aus Formular übernehmen
				'gruppe'       => \Schachbulle\ContaoInternetschachBundle\Classes\Helper::Gruppenzuordnung($arrData['pid'], $spieler['dwz'], true), // Neu aus Formular übernehmen
			);
			// Datensatz updaten
			//print_r($updateSet);
			$objRecord = \Database::getInstance()->prepare('UPDATE tl_internetschach_anmeldungen %s WHERE id = ?')
			                                     ->set($updateSet)
			                                     ->execute($objAnmeldung->id);
			$objVersion->create();
			\System::log('A new version of record "tl_internetschach_anmeldungen.id='.$objAnmeldung->id.'" has been created'.$this->getParentEntries('tl_internetschach_anmeldungen', $objAnmeldung->id), __METHOD__, TL_GENERAL);
			\System::log('[Internetschach] Geänderte Anmeldung: '.$set['name'], __CLASS__.'::'.__FUNCTION__, TL_CRON);
		}
		else
		{
			//print_r($set);
			// Absolut neue Anmeldung
			$set['bemerkungen'] =  $set['bemerkungen'] ? date('d.m.Y H:i').' Uhr: '.$set['bemerkungen'] : $set['bemerkungen']; // Uhrzeit bei Bemerkung ergänzen
			$objRecord = \Database::getInstance()->prepare('INSERT INTO tl_internetschach_anmeldungen %s')
			                                     ->set($set)
			                                     ->execute();
			\System::log('[Internetschach] Neue Anmeldung: '.$set['name'], __CLASS__.'::'.__FUNCTION__, TL_CRON);
		}

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
			$objEmail->subject = $objMain->titel.' - '.($objAnmeldung->numRows ? 'Update ' : '').'Anmeldung '.$set['name'];

			// Text zusammenbauen
			$text = 'Sie haben sich angemeldet für: '.$objMain->titel."\n\nFolgende Daten wurden an uns übertragen:\n\n";
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
						$text .= 'Bemerkungen: '.$bemerkungen."\n";
						break;
					case 'verein':
						$text .= 'Verein: '.$value."\n";
						break;
					case 'name':
						$text .= 'Name: '.$value."\n";
						break;
					case 'dwz':
						$text .= 'DWZ: '.($value ? $value : '-')."\n";
						break;
					case 'fideElo':
						$text .= 'FIDE-Elo: '.($value ? $value : '-')."\n";
						break;
					case 'fideTitel':
						$text .= 'FIDE-Titel: '.$value."\n";
						break;
					case 'turniere':
						$temp = (array)unserialize($value);
						$turniere = \Schachbulle\ContaoInternetschachBundle\Classes\Helper::ArrayToTurniernamen($arrData['pid'], $temp);
						$text .= 'Gemeldete Turniere: '.implode(', ', $turniere)."\n";
						break;
					case 'gruppe':
						$text .= 'Sie sind spielberechtigt für: '.\Schachbulle\ContaoInternetschachBundle\Classes\Helper::Gruppenzuordnung($arrData['pid'], $spieler['dwz'])."\n";
						break;
					default:
				}
			}
			$text .= "\nVielen Dank für Ihre Anmeldung!\nSie stehen unter Vorbehalt der Prüfung bereits auf der Meldeliste.\n\nIhr Deutscher Schachbund";
			$objEmail->text = $text;

			$objEmail->sendBcc($to);
			$objEmail->replyTo($replyto);
			$objEmail->sendTo($set['email']);
		}
	}
}
