<?php

namespace Schachbulle\ContaoInternetschachBundle\Classes;

if (!defined('TL_ROOT')) die('You cannot access this file directly!');


/**
 * Class dsb_trainerlizenzExport
  */
class Export extends \Backend
{

	public function getExcel()
	{
		if ($this->Input->get('key') != 'exportXLS')
		{
			return '';
		}

		// Turnierserie einlesen
		$objSerie = \Database::getInstance()->prepare('SELECT * FROM tl_internetschach WHERE id = ?')
		                                    ->execute(\Input::get('id'));

		// Neues Excel-Objekt erstellen
		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		
		// Dokument-Eigenschaften setzen
		$spreadsheet->getProperties()->setCreator('ContaoInternetschachBundle')
		            ->setLastModifiedBy('ContaoInternetschachBundle')
		            ->setTitle('Anmeldungen '.$objSerie->titel)
		            ->setSubject('Anmeldungen '.$objSerie->titel)
		            ->setDescription('Liste der Anmeldungen '.$objSerie->titel)
		            ->setKeywords('schach anmeldungen internet')
		            ->setCategory('Export Anmeldungen '.$objSerie->titel);

		// Bereits vorhandene Tabellenblätter löschen (funktioniert nicht)
		$anzahl = $spreadsheet->getSheetCount();
		for($x = $anzahl; $x < $anzahl; $x++)
		{
			$spreadsheet->removeSheetByIndex($x);
		}

		// Daten laden
		$records = \Database::getInstance()->prepare('SELECT * FROM tl_internetschach_anmeldungen WHERE pid = ? AND published = ?')
		                                   ->execute(\Input::get('id'), 1);

		$daten = array();
		$sheets = array('alle');
		if($records->numRows)
		{
			while($records->next())
			{
				$turniere = unserialize($records->turniere); // Turniere sind als serialisiertes Array gespeichert
				$accounts = explode(',', $records->chessbase); // Accounts ChessBase sind so getrennt: Nick1,Nick2
				if($turniere)
				{
					foreach($turniere as $turnier)
					{
						foreach($accounts as $account)
						{
							// Tabellenname festlegen und Anmeldung in Array speichern
							$tabellenname = $turnier.'_'.$records->gruppe;
							$sheets[] = $tabellenname; // Tabelle hinzufügen für Exceldatei
							$daten[$tabellenname][] = array
							(
								'gruppe'   => $records->gruppe,
								'turniere' => '',
								'name'     => $records->name,
								'email'    => $records->email,
								'verein'   => $records->verein,
								'account'  => $account,
								'dwz'      => $records->dwz,
								'titel'    => $records->fideTitel
							);
						}
					}
					$daten['alle'][] = array
					(
						'gruppe'   => $records->gruppe,
						'turniere' => implode(',', $turniere),
						'name'     => $records->name,
						'email'    => $records->email,
						'verein'   => $records->verein,
						'account'  => $account,
						'dwz'      => $records->dwz,
						'titel'    => $records->fideTitel
					);
				}
			}
		}

		// Tabellenblätter anlegen, zuvor doppelte Einträge aus sheets-Array entfernen
		$sheets = array_unique($sheets);
		$styleArray = [
		    'font' => [
		        'bold' => true,
		    ],
		    'alignment' => [
		        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
		    ],
		    'borders' => [
		        'bottom' => [
		            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
		        ],
		    ],
		    'fill' => [
		        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
		        'rotation' => 90,
		        'startColor' => [
		            'argb' => 'FFA0A0A0',
		        ],
		        'endColor' => [
		            'argb' => 'FFFFFFFF',
		        ],
		    ],
		];
		$styleArray2 = [
		    'alignment' => [
		        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
		    ],
		];
		$i = 0;
		foreach($sheets as $sheet)
		{
			$spreadsheet->createSheet();
			// Blatt aktivieren und Kopfzeile setzen
			$spreadsheet->setActiveSheetIndex($i);
			foreach(range('A','H') as $columnID)
			{
				$spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
			}
			$spreadsheet->getActiveSheet()->getStyle('A1:I1')->applyFromArray($styleArray);
			$spreadsheet->getActiveSheet()->getStyle('A2:I1000')->applyFromArray($styleArray2);
			$spreadsheet->getActiveSheet()->setTitle($sheet)
			            ->setCellValue('A1', 'Gruppe')
			            ->setCellValue('B1', 'Turniere')
			            ->setCellValue('C1', 'Nachname')
			            ->setCellValue('D1', 'Vorname')
			            ->setCellValue('E1', 'E-Mail')
			            ->setCellValue('F1', 'Verein')
			            ->setCellValue('G1', 'DWZ')
			            ->setCellValue('H1', 'Titel')
			            ->setCellValue('I1', 'ChessBase');
			$zeile = 2;
			if($daten[$sheet])
			{
				foreach($daten[$sheet] as $item)
				{
					$name = explode(',', $item['name']); // Name aufteilen
					$spreadsheet->getActiveSheet()
					            ->setCellValue('A'.$zeile, $item['gruppe'])
					            ->setCellValue('B'.$zeile, $item['turniere'])
					            ->setCellValue('C'.$zeile, $name[0])
					            ->setCellValue('D'.$zeile, $name[1])
					            ->setCellValue('E'.$zeile, $item['email'])
					            ->setCellValue('F'.$zeile, $item['verein'])
					            ->setCellValue('G'.$zeile, $item['dwz'])
					            ->setCellValue('H'.$zeile, $item['titel'])
					            ->setCellValue('I'.$zeile, $item['account']);
					$zeile++;
				}
			}
			$i++;
		}
		
		// Rename worksheet
		//$spreadsheet->getActiveSheet()->setTitle('Simple');
		
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$spreadsheet->setActiveSheetIndex(0);
		
		$downloadname = str_replace(array('.', ' '), array('', '_'), $objSerie->titel).'-Anmeldungen_'.date('Ymd-Hi').'.xls';
		$dateiname = str_replace(array('.', ' '), array('', '_'), $objSerie->titel).'-Anmeldungen.xls';

		$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
		$writer->save('bundles/contaointernetschach/'.$dateiname);

		$objMail = new \Email();
		$objMail->subject = 'Anmeldungen '.$objSerie->titel; // ergibt $this->strSubject
		$objMail->text = 'Die aktuelle Liste der Anmeldungen für '.html_entity_decode($objSerie->titel).' findest Du im Anhang!'; // ergibt $this->strHtml

		// Absender "Name <email>" in ein Array $arrFrom aufteilen
		preg_match('~(?:([^<]*?)\s*)?<(.*)>~', html_entity_decode($objSerie->email_sender), $arrFrom);
		$objMail->from = $arrFrom[2];
		$objMail->fromName = $arrFrom[1];

		// fügt eine Datei als Anhang hinzu
		$objMail->attachFile('bundles/contaointernetschach/'.$dateiname);

		$to = explode("\n", html_entity_decode($objSerie->email_export));
		$objMail->sendTo($to);

		// Redirect output to a client’s web browser (Xls)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$downloadname.'"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		
		// If you're serving to IE over SSL, then the following may be needed
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0
		
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
		$writer->save('php://output');
	}

	public function copyToAnmeldung()
	{
		if($this->Input->get('key') != 'copyToAnmeldung')
		{
			return '';
		}

		// Spielerdaten einlesen
		$objSpieler = \Database::getInstance()->prepare('SELECT * FROM tl_internetschach_spieler WHERE id = ?')
		                                      ->execute(\Input::get('id'));

		if($objSpieler->numRows)
		{
			$zeit = time();
			// Anmeldung vorbereiten
			$set = array
			(
				'tstamp'       => $zeit,
				'pid'          => $objSpieler->pid,
				'playerId'     => $objSpieler->id,
				'verein'       => $objSpieler->verein,
				'name'         => $objSpieler->name,
				'geschlecht'   => $objSpieler->geschlecht,
				'geburtsjahr'  => $objSpieler->geburtsjahr,
				'dwz'          => $objSpieler->dwz,
				'fideElo'      => $objSpieler->fideElo,
				'fideTitel'    => $objSpieler->fideTitel,
				'registerDate' => $zeit,
				'intern'       => 'Manueller Import aus tl_internetschach_spieler am '.date('d.m.Y H:i', $zeit)
			);

			// Anmeldung anlegen
			$objInsert = \Database::getInstance()->prepare('INSERT INTO tl_internetschach_anmeldungen %s')
			                                     ->set($set)
			                                     ->execute();

		}

		// Zurück zur Seite
		$search = array('&key=copyToAnmeldung', '&id='.$this->Input->get('id'));
		$replace = array('', '&id='.$objSpieler->pid);
		\Controller::redirect(str_replace($search, $replace, \Environment::get('request')));
	}
}
