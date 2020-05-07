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

		// Neues Excel-Objekt erstellen
		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		
		// Dokument-Eigenschaften setzen
		$spreadsheet->getProperties()->setCreator('Frank Hoppe')
		            ->setLastModifiedBy('Frank Hoppe')
		            ->setTitle('Anmeldungen DISAM')
		            ->setSubject('Anmeldungen DISAM')
		            ->setDescription('Liste der Anmeldungen zur DISAM')
		            ->setKeywords('disam schach anmeldungen internet')
		            ->setCategory('Export DISAM-Anmeldungen');

		// Bereits vorhandene Tabellenblätter löschen (funktioniert nicht)
		$anzahl = $spreadsheet->getSheetCount();
		for($x = $anzahl; $x < $anzahl; $x++)
		{
			$spreadsheet->removeSheetByIndex($x);
		}

		// Daten laden
		$records = \Database::getInstance()->prepare('SELECT * FROM ctlg_disam_anmeldungen WHERE jahr=? AND mitglied=? AND invisible=?')
		                                   ->execute(2020, 1, '');

		$daten = array();
		if($records->numRows)
		{
			while($records->next())
			{
				$turniere = explode(',', $records->turniere); // Turniere sind so getrennt: 1,2,3,F
				$accounts = explode('|', $records->account); // Accounts ChessBase sind so getrennt: Nick1|Nick2
				foreach($turniere as $turnier)
				{
					foreach($accounts as $account)
					{
						// Tabellenname festlegen und Anmeldung in Array speichern
						switch($turnier)
						{
							case '1': $name = 'Vor1_'.$records->gruppe; break;
							case '2': $name = 'Vor2_'.$records->gruppe; break;
							case '3': $name = 'Vor3_'.$records->gruppe; break;
							case 'F': $name = 'Fin_'.$records->gruppe; break;
							default: $name = '';
						}
						$daten[$name][] = array
						(
							'gruppe'   => $records->gruppe,
							'turniere' => '',
							'name'     => $records->nachname.','.$records->vorname,
							'verein'   => $records->verein,
							'account'  => $account,
							'dwz'      => $records->dwz,
							'titel'    => $records->titel,
							'finale'   => in_array('F', $turniere) ? 'X' : ''
						);
					}
				}
				$daten['alle'][] = array
				(
					'gruppe'   => $records->gruppe,
					'turniere' => $records->turniere,
					'name'     => $records->nachname.','.$records->vorname,
					'verein'   => $records->verein,
					'account'  => $records->account,
					'dwz'      => $records->dwz,
					'titel'    => $records->titel,
					'finale'   => in_array('F', $turniere) ? 'X' : ''
				);
			}
		}

		// Tabellenblätter anlegen
		$sheets = array('Vor1_A', 'Vor1_B', 'Vor1_C', 'Vor2_A', 'Vor2_B', 'Vor2_C', 'Vor3_A', 'Vor3_B', 'Vor3_C', 'Fin_A', 'Fin_B', 'Fin_C', 'alle');
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
			foreach(range('A','G') as $columnID)
			{
				$spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
			}
			$spreadsheet->getActiveSheet()->getStyle('A1:H1')->applyFromArray($styleArray);
			$spreadsheet->getActiveSheet()->getStyle('A2:H1000')->applyFromArray($styleArray2);
			$spreadsheet->getActiveSheet()->setTitle($sheet)
			            ->setCellValue('A1', 'Gruppe')
			            ->setCellValue('B1', 'Turniere')
			            ->setCellValue('C1', 'Name,Vorname')
			            ->setCellValue('D1', 'Verein')
			            ->setCellValue('E1', 'DWZ')
			            ->setCellValue('F1', 'Titel')
			            ->setCellValue('G1', 'ChessBase')
			            ->setCellValue('H1', 'Finale');
			$zeile = 2;
			if($daten[$sheet])
			{
				foreach($daten[$sheet] as $item)
				{
					$spreadsheet->getActiveSheet()
					            ->setCellValue('A'.$zeile, $item['gruppe'])
					            ->setCellValue('B'.$zeile, $item['turniere'])
					            ->setCellValue('C'.$zeile, $item['name'])
					            ->setCellValue('D'.$zeile, $item['verein'])
					            ->setCellValue('E'.$zeile, $item['dwz'])
					            ->setCellValue('F'.$zeile, $item['titel'])
					            ->setCellValue('G'.$zeile, $item['account'])
					            ->setCellValue('H'.$zeile, $item['finale']);
					$zeile++;
				}
			}
			$i++;
		}
		
		// Rename worksheet
		//$spreadsheet->getActiveSheet()->setTitle('Simple');
		
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$spreadsheet->setActiveSheetIndex(0);
		
		$downloadname = 'DISAM-Anmeldungen_'.date('Ymd-Hi').'.xls';
		$dateiname = 'DISAM-Anmeldungen.xls';

		$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
		$writer->save('bundles/contaointernetschach/'.$dateiname);

		$objMail = new \Email();
		$objMail->subject = 'Anmeldungen DISAM'; // ergibt $this->strSubject
		$objMail->text = 'Die aktuelle Liste der Anmeldungen für die DISAM findest Du im Anhang!'; // ergibt $this->strHtml
		$objMail->from = 'webmaster@schachbund.de'; // ergibt $this->strSender
		$objMail->fromName = 'Frank Hoppe'; // ergibt $this->strSenderName
		// fügt eine Datei als Anhang hinzu
		$objMail->attachFile('bundles/contaodisam/'.$dateiname);
		$objMail->sendBcc('Frank Hoppe <webmaster@schachbund.de>'); 
		$objMail->sendCc('Reinhold Goldau <goldaureinhold@gmail.com>'); 
		$objMail->sendTo('DISAM-Turnierleitung <disam@schachbund.de>');

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
