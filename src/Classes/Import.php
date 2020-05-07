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
}
