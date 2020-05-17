<?php

namespace Schachbulle\ContaoInternetschachBundle\Classes;

if (!defined('TL_ROOT')) die('You cannot access this file directly!');


/**
 * Class dsb_trainerlizenzImport
  */
class Import extends \Backend
{

	/**
	 * Return a form to choose a CSV file and import it
	 * @param object
	 * @return string
	 */
	public function importCSV(\DataContainer $dc)
	{
		if (\Input::get('key') != 'importCSV')
		{
			return '';
		}

		$this->import('BackendUser', 'User');
		$class = $this->User->uploader;

		// See #4086
		if (!class_exists($class))
		{
			$class = 'FileUpload';
		}

		$objUploader = new $class();

		// Importiere die Daten, wenn das Formular abgeschickt wurde
		if (\Input::post('FORM_SUBMIT') == 'tl_internetschach_import')
		{
			$arrUploaded = $objUploader->uploadTo('system/tmp');

			if(empty($arrUploaded))
			{
				\Message::addError($GLOBALS['TL_LANG']['ERR']['all_fields']);
				$this->reload();
			}

			$this->import('Database');

			foreach($arrUploaded as $strFile)
			{
				$objFile = new \File($strFile, true);

				if($objFile->extension != 'zip')
				{
					\Message::addError(sprintf($GLOBALS['TL_LANG']['ERR']['filetype'], $objFile->extension));
					continue;
				}

				// ZIP-Datei entpacken
				$zip = new \ZipArchive;
				$res = $zip->open($objFile->dirname.'/'.$objFile->basename);
				if($res === true)
				{
					// Entpacke ZIP-Archiv in system/tmp
					$zip->extractTo($objFile->dirname);
					$zip->close();

					// vereine.csv einlesen
					$fp = fopen($objFile->dirname.'/vereine.csv','r');
					$verein = array();
					while(($arrRow = @fgetcsv($fp, null, ',')) !== false)
					{
						$escape_arrRow = array();
						foreach($arrRow as $wert)
						{
							$wert = addslashes($wert);
							$escape_arrRow[] = $wert;
						}
						unset($wert);
						if($escape_arrRow[0] != 'ZPS') $verein[$escape_arrRow[0]] = utf8_encode($escape_arrRow[3]);
					}
					fclose($fp);

					// spieler.csv importieren
					$fp = fopen($objFile->dirname.'/spieler.csv','r');
					$arrImport = array();
					while(($arrRow = @fgetcsv($fp, null, ',')) !== false)
					{
						$escape_arrRow = array();
						foreach($arrRow as $wert)
						{
							$wert = addslashes($wert);
							$escape_arrRow[] = $wert;
						}
						unset($wert);
						if($escape_arrRow[0] != 'ZPS')
						{
							// Spieler in Import-Array aufnehmen
							$arrImport[] = array
							(
								'pid'                => \Input::get('id'),
								'zps'                => $escape_arrRow[0],
								'mglnr'              => $escape_arrRow[1],
								'verein'             => $verein[$escape_arrRow[0]],
								'status'             => $escape_arrRow[2],
								'name'               => utf8_encode($escape_arrRow[3]),
								'geschlecht'         => $escape_arrRow[4],
								'spielberechtigung'  => $escape_arrRow[5],
								'geburtsjahr'        => $escape_arrRow[6],
								'dwz'                => $escape_arrRow[8],
								'fideElo'            => $escape_arrRow[10],
								'fideTitel'          => $escape_arrRow[11],
								'fideID'             => $escape_arrRow[12],
								'fideNation'         => $escape_arrRow[13],
								'published'          => 1
							);
						}
					}
					fclose($fp);

					// Alte Datensätze löschen
					$this->Database->prepare('DELETE FROM tl_internetschach_spieler WHERE pid = ?')
					               ->execute(\Input::get('id'));

					// SQL generieren
					$i = 0;
					foreach($arrImport as $arrRecord)
					{
						if($i == 0)
						{
							$sql = 'INSERT INTO tl_internetschach_spieler ('.implode(',',array_keys($arrRecord)).') VALUES';
							$sql .= ' ("'.implode('","',array_values($arrRecord)).'"),';
						}
						else
						{
							$sql .= ' ("'.implode('","',array_values($arrRecord)).'"),';
						}

						// Daten eintragen, wenn 1000 Spieler erreicht sind
						if($i == 1000)
						{
							$sql = substr($sql, 0, -1).';';
							// in Datenbank eintragen
							$this->Database->prepare($sql)
							               ->execute(\Input::get('id'));
							$sql = '';
							$i = 0;
						}
						else $i++;

					}

					// Restliche Daten eintragen, wenn SQL-String gefüllt
					if($sql)
					{
						$sql = substr($sql, 0, -1).';';
						// in Datenbank eintragen
						$this->Database->prepare($sql)
						               ->execute(\Input::get('id'));
					}

				}
			}

			\System::setCookie('BE_PAGE_OFFSET', 0, 0);
			$this->redirect(str_replace('&key=importCSV', '', \Environment::get('request')));
		}

		// Return form
		return '
<div id="tl_buttons">
<a href="'.ampersand(str_replace('&key=importCSV', '', \Environment::get('request'))).'" class="header_back" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']).'" accesskey="b">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a>
</div>

<h2 class="sub_headline">'.$GLOBALS['TL_LANG']['MOD']['internetschach_import_headline'][1].'</h2>
'.\Message::generate().'
<form action="'.ampersand(\Environment::get('request'), true).'" id="tl_internetschach_import" class="tl_form" method="post" enctype="multipart/form-data">
<div class="tl_formbody_edit">
<input type="hidden" name="FORM_SUBMIT" value="tl_internetschach_import">
<input type="hidden" name="REQUEST_TOKEN" value="'.REQUEST_TOKEN.'">

<fieldset class="tl_tbox nolegend">
  <div class="widget w50">
    <h3>'.$GLOBALS['TL_LANG']['MOD']['internetschach_import_file'][0].'</h3>'.$objUploader->generateMarkup().(isset($GLOBALS['TL_LANG']['MOD']['internetschach_import'][1]) ? '
    <p class="tl_help tl_tip">'.$GLOBALS['TL_LANG']['MOD']['internetschach_import_file'][1].'</p>' : '').'
  </div>
</fieldset>
</div>

<div class="tl_formbody_submit">

<div class="tl_submit_container">
  <input type="submit" name="save" id="save" class="tl_submit" accesskey="s" value="'.specialchars($GLOBALS['TL_LANG']['MOD']['internetschach_import_submit'][0]).'">
</div>

</div>
</form>';
	}

	/**
	 * Return a form to choose a CSV file and import it
	 * @param object
	 * @return string
	 */
	public function importTable(\DataContainer $dc)
	{
		if (\Input::get('key') != 'importTable')
		{
			return '';
		}

		$this->import('BackendUser', 'User');
		$class = $this->User->uploader;

		// See #4086
		if (!class_exists($class))
		{
			$class = 'FileUpload';
		}

		$objUploader = new $class();

		// Importiere die Daten, wenn das Formular abgeschickt wurde
		if (\Input::post('FORM_SUBMIT') == 'tl_internetschach_importTable')
		{
			$arrUploaded = $objUploader->uploadTo('system/tmp');

			if(empty($arrUploaded))
			{
				\Message::addError($GLOBALS['TL_LANG']['ERR']['all_fields']);
				$this->reload();
			}

			$this->import('Database');

			foreach($arrUploaded as $strFile)
			{
				$objFile = new \File($strFile, true);

				// Datei einlesen
				$daten = file_get_contents($objFile->dirname.'/'.$objFile->basename);

				if($objFile->extension == 'html' || $objFile->extension == 'htm')
				{
					// HTML-Import
					$tabelle = self::ImportHTML($daten);
					$csv = self::ConvertToCSV($tabelle);
				}
				elseif($objFile->extension == 'json')
				{
					// JSON-Import
				}
				else
				{
					// Falsches Format
					\Message::addError(sprintf($GLOBALS['TL_LANG']['ERR']['filetype'], $objFile->extension));
					continue;
					$tabelle = '';
				}

				$set = array
				(
					'csv'         => $csv,
					'importRaw'   => $daten,
					'importArray' => serialize($tabelle)
				);
				$this->Database->prepare('UPDATE tl_internetschach_tabellen %s WHERE id = ?')
				               ->set($set)
				               ->execute(\Input::get('id'));

			}

			\System::setCookie('BE_PAGE_OFFSET', 0, 0);
			$this->redirect(str_replace('&key=importTable', '&act=edit', \Environment::get('request')));

		}

		// Return form
		return '
<div id="tl_buttons">
<a href="'.ampersand(str_replace('&key=importTable', '', \Environment::get('request'))).'" class="header_back" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']).'" accesskey="b">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a>
</div>

<h2 class="sub_headline">'.$GLOBALS['TL_LANG']['MOD']['internetschach_importTable_headline'][1].'</h2>
'.\Message::generate().'
<form action="'.ampersand(\Environment::get('request'), true).'" id="tl_internetschach_importTable" class="tl_form" method="post" enctype="multipart/form-data">
<div class="tl_formbody_edit">
<input type="hidden" name="FORM_SUBMIT" value="tl_internetschach_importTable">
<input type="hidden" name="REQUEST_TOKEN" value="'.REQUEST_TOKEN.'">

<fieldset class="tl_tbox nolegend">
  <div class="widget w50">
    <h3>'.$GLOBALS['TL_LANG']['MOD']['internetschach_importTable_file'][0].'</h3>'.$objUploader->generateMarkup().(isset($GLOBALS['TL_LANG']['MOD']['internetschach_importTable'][1]) ? '
    <p class="tl_help tl_tip">'.$GLOBALS['TL_LANG']['MOD']['internetschach_importTable_file'][1].'</p>' : '').'
  </div>
</fieldset>
</div>

<div class="tl_formbody_submit">

<div class="tl_submit_container">
  <input type="submit" name="save" id="save" class="tl_submit" accesskey="s" value="'.specialchars($GLOBALS['TL_LANG']['MOD']['internetschach_importTable_submit'][0]).'">
</div>

</div>
</form>';
	}

	private function ImportHTML($string)
	{
		$string = str_replace(array('<th', '</th>'), array('<td', '</td>'), $string);
		$string = iconv('windows-1251', 'utf-8', $string); // Bug in paquettg/php-html-parser umgehen, https://github.com/paquettg/php-html-parser/issues/209#event-3327333893
		
		$dom = new \PHPHtmlParser\Dom;
		$dom->load($string);
		$table = $dom->find('table')[0];
		$rows = $table->find('tr');
		$tabelle = array();
		$daten = array();
		$rowNr = 0;
		foreach($rows as $row)
		{
			$cols = $row->find('td');
			$colNr = 0;
			$i = 0;
			foreach($cols as $col)
			{
				$colspan =  $col->getAttribute('colspan');
				if(!$colspan) $colspan = 1;
				$value = $col->innerHtml;
		
				// Rundenanzahl feststellen
				if($rowNr == 0 && $colNr == 0) $runden = count($cols) - 4;
		
				for($x = 0; $x < $colspan; $x++)
				{
					if($i == 0)	$name = 'platz';
					elseif($i == 1)	$name = 'cb-name';
					elseif($i == 2)	$name = 'cb-land';
					elseif($i == 3)	$name = 'cb-rating';
					elseif($i == $runden + 4) $name = 'punkte';
					elseif($i == $runden + 5) $name = 'wertung1';
					elseif($i == $runden + 6) $name = 'wertung2';
					else
					{
						$name = 'runden';
						$rundeIndex = $i - 4;
					}
		
					$array = array();
					preg_match('/src="([^"]*)"/i', $value, $array);
					$land = $array[1];
		
					$value = str_replace('&nbsp;', '', $value);
					$value = strip_tags($value);
					// Tabellenzelle schreiben
					if($name == 'runden') $tabelle[$rowNr][$name][$rundeIndex] = str_replace(array('&diams;', '&loz;'), array('s', 'w'), $value);
					elseif($name == 'cb-land') $tabelle[$rowNr][$name] = str_replace(array('flags/nat16_', '.gif'), array('', ''), $land);
					else $tabelle[$rowNr][$name] = $value;
					$i++;
				}
				$colNr++;
			}
			$rowNr++;
		}
		return $tabelle;
	}

	private function ConvertToCSV($tabelle)
	{

		// Spaltenbreiten ermitteln
		$breite = $tabelle[0];
		for($x = 0; $x < count($tabelle); $x++)
		{
			if($x == 0)
			{
				$breite['platz'] = 3;
				$breite['cb-name'] = 8;
				$breite['cb-land'] = 4;
				$breite['cb-rating'] = 3;
				$breite['punkte'] = 4;
				$breite['wertung1'] = 4;
				$breite['wertung2'] = 4;
				for($y = 0; $y < count($tabelle[$x]['runden']); $y++)
				{
					$breite['runden'][$y] = strlen($tabelle[$x]['runden'][$y]);
				}
			}
			else
			{
				$breite['platz'] = strlen($tabelle[$x]['platz']) > $breite['platz'] ? strlen($tabelle[$x]['platz']) : $breite['platz'];
				$breite['cb-name'] = strlen($tabelle[$x]['cb-name']) > $breite['cb-name'] ? strlen($tabelle[$x]['cb-name']) : $breite['cb-name'];
				$breite['cb-land'] = strlen($tabelle[$x]['cb-land']) > $breite['cb-land'] ? strlen($tabelle[$x]['cb-land']) : $breite['cb-land'];
				$breite['cb-rating'] = strlen($tabelle[$x]['cb-rating']) > $breite['cb-rating'] ? strlen($tabelle[$x]['cb-rating']) : $breite['cb-rating'];
				$breite['punkte'] = strlen($tabelle[$x]['punkte']) > $breite['punkte'] ? strlen($tabelle[$x]['punkte']) : $breite['punkte'];
				$breite['wertung1'] = strlen($tabelle[$x]['wertung1']) > $breite['wertung1'] ? strlen($tabelle[$x]['wertung1']) : $breite['wertung1'];
				$breite['wertung2'] = strlen($tabelle[$x]['wertung2']) > $breite['wertung2'] ? strlen($tabelle[$x]['wertung2']) : $breite['wertung2'];
				for($y = 0; $y < count($tabelle[$x]['runden']); $y++)
				{
					$breite['runden'][$y] = strlen($tabelle[$x]['runden'][$y]) > $breite['runden'][$y] ? strlen($tabelle[$x]['runden'][$y]) : $breite['runden'][$y];
				}
			}
		}

		$csv = '';
		for($x = 0; $x < count($tabelle); $x++)
		{
			if($x == 0)
			{
				$csv = 'Pl.;Benutzer;Land;CBR;Pkt.;SoBe;Wtg2;';
				$csv = substr('Pl.'.str_repeat(' ', 100), 0, $breite['platz']).';';
				$csv .= substr('Benutzer'.str_repeat(' ', 100), 0, $breite['cb-name']).';';
				$csv .= substr('Land'.str_repeat(' ', 100), 0, $breite['cb-land']).';';
				$csv .= substr('CBR'.str_repeat(' ', 100), 0, $breite['cb-rating']).';';
				$csv .= substr('Pkt.'.str_repeat(' ', 100), 0, $breite['punkte']).';';
				$csv .= substr('SoBe'.str_repeat(' ', 100), 0, $breite['wertung1']).';';
				$csv .= substr('Wtg2'.str_repeat(' ', 100), 0, $breite['wertung2']).';';

				for($y = 0; $y < count($tabelle[$x]['runden']); $y++)
				{
					$csv .= substr($tabelle[$x]['runden'][$y].str_repeat(' ', 100), 0, $breite['runden'][$y]).';';
				}
				$csv = substr($csv, 0, -1)."\n";
			}
			else
			{
				$csv .= mb_substr($tabelle[$x]['platz'].str_repeat(' ', 100), 0, $breite['platz']).';';
				$csv .= mb_substr($tabelle[$x]['cb-name'].str_repeat(' ', 100), 0, $breite['cb-name']).';';
				$csv .= mb_substr($tabelle[$x]['cb-land'].str_repeat(' ', 100), 0, $breite['cb-land']).';';
				$csv .= mb_substr($tabelle[$x]['cb-rating'].str_repeat(' ', 100), 0, $breite['cb-rating']).';';
				$csv .= mb_substr($tabelle[$x]['punkte'].str_repeat(' ', 100), 0, $breite['punkte']).';';
				$csv .= mb_substr($tabelle[$x]['wertung1'].str_repeat(' ', 100), 0, $breite['wertung1']).';';
				$csv .= mb_substr($tabelle[$x]['wertung2'].str_repeat(' ', 100), 0, $breite['wertung2']).';';
				for($y = 0; $y < count($tabelle[$x]['runden']); $y++)
				{
					$csv .= mb_substr($tabelle[$x]['runden'][$y].str_repeat(' ', 100), 0, $breite['runden'][$y]).';';
				}
				$csv = substr($csv, 0, -1)."\n";
			}
		}
		return $csv;
	}
}
