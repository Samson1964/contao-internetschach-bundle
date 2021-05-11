<?php

namespace Schachbulle\ContaoInternetschachBundle\Classes;

if (!defined('TL_ROOT')) die('You cannot access this file directly!');


/**
 * Class dsb_trainerlizenzExport
  */
class ExportPreise extends \Backend
{

	public function getExcel()
	{
		if ($this->Input->get('key') != 'exportPreiseXLS')
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
		            ->setTitle('Preise '.$objSerie->titel)
		            ->setSubject('Preise '.$objSerie->titel)
		            ->setDescription('Liste der Preise und Gewinner '.$objSerie->titel)
		            ->setKeywords('schach preise internet')
		            ->setCategory('Export Preise '.$objSerie->titel);

		// Bereits vorhandene Tabellenblätter löschen (funktioniert nicht)
		$anzahl = $spreadsheet->getSheetCount();
		for($x = $anzahl; $x < $anzahl; $x++)
		{
			$spreadsheet->removeSheetByIndex($x);
		}

		// Preise laden
		$objPreise = \Database::getInstance()->prepare('SELECT * FROM tl_internetschach_preise WHERE pid = ? AND published = ? ORDER BY gruppe ASC, turnier ASC, dwz_grenze ASC, platz ASC')
		                                     ->execute(\Input::get('id'), 1);
		$preiseArr = array();
		if($objPreise->numRows)
		{
			while($objPreise->next())
			{
				$preiseArr[] = array
				(
					'id'         => $objPreise->id,
					'name'       => $objPreise->name,
					'platz'      => $objPreise->platz,
					'dwz_grenze' => $objPreise->dwz_grenze,
					'turnier'    => $objPreise->turnier,
					'gruppe'     => $objPreise->gruppe
				);
			}
		}

		// Tabellen laden
		$objTabellen = \Database::getInstance()->prepare('SELECT * FROM tl_internetschach_tabellen WHERE pid = ? AND published = ?')
		                                       ->execute(\Input::get('id'), 1);
		$tabellenArr = array();
		if($objTabellen->numRows)
		{
			while($objTabellen->next())
			{
				$tabellenArr[] = array
				(
					'id'          => $objTabellen->id,
					'turnier'     => $objTabellen->turnier,
					'gruppe'      => $objTabellen->gruppe,
					'tabelle'     => unserialize($objTabellen->importArray)
				);
			}
		}

		//echo "<pre>";
		//print_r($preiseArr);
		//print_r($tabellenArr);
		//echo "</pre>";
		//exit;
		// Gewinner den Preisen zuordnen
		foreach($tabellenArr as $tabelle)
		{
			// Beginnen bei Index 1 (= Platz 1)
			for($i = 1; $i < count($tabelle['tabelle']); $i++)
			{
				if($tabelle['tabelle'][$i]['prices'])
				{
					// Platz hat Preis(e) bekommen, der Reihe nach durchgehen
					foreach($tabelle['tabelle'][$i]['prices'] as $preis_id)
					{
						// Preis-ID suchen und Benutzer eintragen
						for($x = 0; $x < count($preiseArr); $x++)
						{
							if($preiseArr[$x]['id'] == $preis_id)
							{
								$preiseArr[$x]['benutzer'] = $tabelle['tabelle'][$i]['cb-name'];
								$Anmeldung =  \Schachbulle\ContaoInternetschachBundle\Classes\Helper::getAnmeldung(\Input::get('id'), $tabelle['tabelle'][$i]['cb-name']);
								$preiseArr[$x]['klarname'] = $Anmeldung['name'];
								$preiseArr[$x]['dwz'] = $Anmeldung['dwz'];
								$preiseArr[$x]['turnierplatz'] = $tabelle['tabelle'][$i]['platz'];
								$preiseArr[$x]['email'] = $Anmeldung['email'];
								break;
							}
						}
					}
				}
			}
		}


		// Tabellenblätter definieren
		$sheets = array('Preise', 'Gewinner');
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

		// Preise-Tabelle anlegen und füllen
		$spreadsheet->createSheet();
		$spreadsheet->setActiveSheetIndex(0);
		foreach(range('A','H') as $columnID)
		{
			$spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
		}
		$spreadsheet->getActiveSheet()->getStyle('A1:J1')->applyFromArray($styleArray);
		$spreadsheet->getActiveSheet()->getStyle('A2:J1000')->applyFromArray($styleArray2);
		$spreadsheet->getActiveSheet()->setTitle('Preise')
		            ->setCellValue('A1', 'Gruppe')
		            ->setCellValue('B1', 'Turnier')
		            ->setCellValue('C1', 'Platz')
		            ->setCellValue('D1', 'DWZ')
		            ->setCellValue('E1', 'Preis')
		            ->setCellValue('F1', 'Gewinner')
		            ->setCellValue('G1', 'Klarname')
		            ->setCellValue('H1', 'DWZ')
		            ->setCellValue('I1', 'Platz')
		            ->setCellValue('J1', 'E-Mail');

		if($preiseArr)
		{
			$zeile = 2;
			foreach($preiseArr as $item)
			{
				$spreadsheet->getActiveSheet()
				            ->setCellValue('A'.$zeile, \Schachbulle\ContaoInternetschachBundle\Classes\Helper::getGruppe(\Input::get('id'), $item['gruppe']))
				            ->setCellValue('B'.$zeile, \Schachbulle\ContaoInternetschachBundle\Classes\Helper::getTurnier(\Input::get('id'), $item['turnier']))
				            ->setCellValue('C'.$zeile, $item['platz'])
				            ->setCellValue('D'.$zeile, $item['dwz_grenze'] ? '< '.$item['dwz_grenze'] : '')
				            ->setCellValue('E'.$zeile, html_entity_decode($item['name']))
				            ->setCellValue('F'.$zeile, $item['benutzer'])
				            ->setCellValue('G'.$zeile, $item['klarname'])
				            ->setCellValue('H'.$zeile, $item['dwz'])
				            ->setCellValue('I'.$zeile, $item['turnierplatz'])
				            ->setCellValue('J'.$zeile, $item['email']);
				$zeile++;
			}
		}

		// Rename worksheet
		//$spreadsheet->getActiveSheet()->setTitle('Simple');

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$spreadsheet->setActiveSheetIndex(0);

		$downloadname = str_replace(array('.', ' '), array('', '_'), $objSerie->titel).'-Preise_'.date('Ymd-Hi').'.xls';
		$dateiname = str_replace(array('.', ' '), array('', '_'), $objSerie->titel).'-Preise.xls';

		$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
		$writer->save('bundles/contaointernetschach/'.$dateiname);

		$objMail = new \Email();
		$objMail->subject = 'Preise '.$objSerie->titel; // ergibt $this->strSubject
		$objMail->text = 'Die aktuelle Liste der Preise und Gewinner für '.html_entity_decode($objSerie->titel).' findest Du im Anhang!'; // ergibt $this->strHtml

		// Absender "Name <email>" in ein Array $arrFrom aufteilen
		preg_match('~(?:([^<]*?)\s*)?<(.*)>~', html_entity_decode($objSerie->email_sender), $arrFrom);
		$objMail->from = $arrFrom[2];
		$objMail->fromName = $arrFrom[1];

		// fügt eine Datei als Anhang hinzu
		$objMail->attachFile('bundles/contaointernetschach/'.$dateiname);

		$to = explode("\n", html_entity_decode($objSerie->email_preise));
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

}
