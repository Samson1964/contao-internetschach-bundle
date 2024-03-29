<?php

namespace Schachbulle\ContaoInternetschachBundle\Classes;

if (!defined('TL_ROOT')) die('You cannot access this file directly!');


/**
 * Class dsb_trainerlizenzExport
  */
class Preise extends \Backend
{

	public function Aktualisieren()
	{
		if($this->Input->get('key') != 'preise')
		{
			return '';
		}

		$aktZeit = time();
		
		// Turnierserie einlesen
		$objSerie = \Database::getInstance()->prepare('SELECT * FROM tl_internetschach WHERE id = ?')
		                                    ->execute(\Input::get('id'));

		// Tabellen einlesen
		$objTabellen = \Database::getInstance()->prepare("SELECT * FROM tl_internetschach_tabellen WHERE pid = ?")
		                                       ->execute($objSerie->id);

		// Turniere/Gruppen der Serie in Arrays laden
		$turniere = unserialize($objSerie->turniere);
		$gruppen = unserialize($objSerie->gruppen);

		// =======================================================
		// START: Wird doch gar nicht verwendet, oder!?
		// =======================================================
		// Benutzernamen der Turniere der jeweiligen Gruppe initialisieren
		// Hier werden die Preise gespeichert
		$Benutzer = array();
		foreach($gruppen as $gruppe)
		{
			if($gruppe['feldname'])
			{
				// Array nur anlegen, wenn Feldname der Gruppe vorhanden
				$Benutzer[$gruppe['feldname']] = array();
			}
		}
		// =======================================================
		// ENDE
		// =======================================================

		// Definierte Turniere der Reihe nach prüfen
		foreach($turniere as $turnier)
		{
			// Definierte Gruppen der Reihe nach prüfen, wenn das Turnier den Spieltermin erreicht hat
			if($aktZeit > $turnier['termin'])
			{
				// Turnier wahrscheinlich schon beendet
				foreach($gruppen as $gruppe)
				{
					if($gruppe['feldname'])
					{
						// Tabelle suchen, vorher Objekt zurücksetzen
						$objTabellen->reset();
						if($objTabellen->numRows)
						{
							while($objTabellen->next())
							{
								//echo "... Tabelle aus Turnier ".$objTabellen->turnier." Gruppe ".$objTabellen->gruppe."<br>";
								if($objTabellen->turnier == $turnier['feldname'] && $objTabellen->gruppe == $gruppe['feldname'] && $objTabellen->importArray)
								{
									$tabelleArr = unserialize($objTabellen->importArray); // Tabelle in Array umwandeln
									// Disqualifizierte Spielernummern laden (Spielernummer = Platz + 1)
									$disqualifiziert = \Schachbulle\ContaoHelperBundle\Classes\Helper::StringToArray($objTabellen->disqualifikation);
                    	
									// Platz-Index der Preise initialisieren, bei 1 anfangen
									$platz = 1;
									$platz_dwz = 1;
                    	
									// Turnier jetzt auswerten
									for($zeile = 0; $zeile < count($tabelleArr); $zeile++)
									{
										// Qualifikation zurücksetzen
										$tabelleArr[$zeile]['prices'] = array();
										if($zeile == 0) continue; // Kopfzeile überspringen
                    	
										// Nur nichtdisqualifizierte Spieler berücksichtigen
										if(!in_array($zeile + 1, $disqualifiziert))
										{
											// Anmeldedaten des Spielers laden, es werden nur angemeldete Spieler berücksichtigt
											$Anmeldung = \Schachbulle\ContaoInternetschachBundle\Classes\Helper::getAnmeldung($objSerie->id, $tabelleArr[$zeile]['cb-name']);
											if($Anmeldung)
											{
												//echo "<pre>";
												//print_r($Anmeldung);
												//echo "</pre>";
												// Nächsten Hauptpreis ermitteln
												$objHauptpreis = \Database::getInstance()->prepare("SELECT * FROM tl_internetschach_preise WHERE pid = ? AND turnier = ? AND gruppe = ? AND platz = ? AND dwz_grenze = ? AND published = ?")
												                                         ->limit(1)
												                                         ->execute($objSerie->id, $objTabellen->turnier, $objTabellen->gruppe, $platz, 0, 1);
												// Nächsten Nebenpreis ermitteln
												$objNebenpreis = \Database::getInstance()->prepare("SELECT * FROM tl_internetschach_preise WHERE pid = ? AND turnier = ? AND gruppe = ? AND platz = ? AND dwz_grenze = ? AND published = ?")
												                                         ->limit(1)
												                                         ->execute($objSerie->id, $objTabellen->turnier, $objTabellen->gruppe, $platz_dwz, $gruppe['dwz_kategoriegrenze'], 1);
												
												//echo "<pre>";
												//echo 'Hauptpreis: ';
												//print_r($objHauptpreis->numRows); echo ' / Nebenpreis: ';
												//print_r($objNebenpreis->numRows); echo ' / DWZ: ';
												//print_r($Anmeldung['dwz']); echo ' / Grenze: ';
												//print_r($objNebenpreis->dwz_grenze); echo ' / Doppelpreise: ';
												//print_r($objSerie->doppelpreise); echo ' / Höhere Preise: ';
												//print_r($objSerie->hoeherepreise);
												//echo "</pre>";
												if($objHauptpreis->numRows && $objNebenpreis->numRows && $Anmeldung['dwz'] < $objNebenpreis->dwz_grenze)
												{
													// Hauptpreis vorhanden, Nebenpreis vorhanden, Anmelde-DWZ unterhalb DWZ-Grenze
													if($objSerie->doppelpreise)
													{
														// Doppelpreise erlaubt, Spieler bekommt beide Preise
														$tabelleArr[$zeile]['prices'][] = $objHauptpreis->id;
														$platz++;
														$tabelleArr[$zeile]['prices'][] = $objNebenpreis->id;
														$platz_dwz++;
													}
													elseif($objSerie->hoeherepreise)
													{
														// Nur ein Preis möglich, nämlich der höhere
														if($objHauptpreis->wert < $objNebenpreis->wert)
														{
															// Nebenpreis ist höherwertiger als der Hauptpreis
															$tabelleArr[$zeile]['prices'][] = $objNebenpreis->id;
															$platz_dwz++;
														}
														else
														{
															// Hauptpreis nehmen, da Nebenpreis nicht höherwertiger
															$tabelleArr[$zeile]['prices'][] = $objHauptpreis->id;
															$platz++;
														}
													}
													else
													{
														// Hauptpreis nehmen
														$tabelleArr[$zeile]['prices'][] = $objHauptpreis->id;
														$platz++;
													}
												}
												elseif(!$objHauptpreis->numRows && $objNebenpreis->numRows && $Anmeldung['dwz'] < $objNebenpreis->dwz_grenze)
												{
													// Hauptpreis nicht vorhanden, Nebenpreis vorhanden, Anmelde-DWZ unterhalb DWZ-Grenze
													$tabelleArr[$zeile]['prices'][] = $objNebenpreis->id;
													$platz_dwz++;
												}
												elseif($objHauptpreis->numRows)
												{
													// Hauptpreis vorhanden, Nebenpreis zu vernachlässigen
													$tabelleArr[$zeile]['prices'][] = $objHauptpreis->id;
													$platz++;
												}
											}
										}
									}
                    	
									//echo "<pre>";
									//print_r($tabelleArr);
									//echo "</pre>";
									// Tabelle aktualisieren
									$set = array
									(
										'importArray' => serialize($tabelleArr)
									);
									$objDB = \Database::getInstance()->prepare("UPDATE tl_internetschach_tabellen %s WHERE id = ?")
									                                 ->set($set)
									                                 ->execute($objTabellen->id);
								}
							}
						}
					}
					else
					{
						// $gruppe['feldname'] ist leer, also ein Turnier ohne Gruppen
						// Tabelle suchen, vorher Objekt zurücksetzen
						$objTabellen->reset();
						if($objTabellen->numRows)
						{
							while($objTabellen->next())
							{
								//echo "... Tabelle aus Turnier ".$objTabellen->turnier." Gruppe ".$objTabellen->gruppe."<br>";
								if($objTabellen->turnier == $turnier['feldname'] && $objTabellen->importArray)
								{
									$tabelleArr = unserialize($objTabellen->importArray); // Tabelle in Array umwandeln
									// Disqualifizierte Spielernummern laden (Spielernummer = Platz + 1)
									$disqualifiziert = \Schachbulle\ContaoHelperBundle\Classes\Helper::StringToArray($objTabellen->disqualifikation);
                    	
									// Platz-Index der Preise initialisieren, bei 1 anfangen
									$platz = 1;
									$platz_dwz = 1;
                    	
									// Turnier jetzt auswerten
									for($zeile = 0; $zeile < count($tabelleArr); $zeile++)
									{
										// Qualifikation zurücksetzen
										$tabelleArr[$zeile]['prices'] = array();
										if($zeile == 0) continue; // Kopfzeile überspringen
                    	
										// Nur nichtdisqualifizierte Spieler berücksichtigen
										if(!in_array($zeile + 1, $disqualifiziert))
										{
											// Anmeldedaten des Spielers laden, es werden nur angemeldete Spieler berücksichtigt
											$Anmeldung = \Schachbulle\ContaoInternetschachBundle\Classes\Helper::getAnmeldung($objSerie->id, $tabelleArr[$zeile]['cb-name']);
											if($Anmeldung)
											{
												//echo "<pre>";
												//print_r($Anmeldung);
												//echo "</pre>";
												// Nächsten Hauptpreis ermitteln
												$objHauptpreis = \Database::getInstance()->prepare("SELECT * FROM tl_internetschach_preise WHERE pid = ? AND turnier = ? AND platz = ? AND dwz_grenze = ? AND published = ?")
												                                         ->limit(1)
												                                         ->execute($objSerie->id, $objTabellen->turnier, $platz, 0, 1);
												// Nächsten Nebenpreis ermitteln
												$objNebenpreis = \Database::getInstance()->prepare("SELECT * FROM tl_internetschach_preise WHERE pid = ? AND turnier = ? AND platz = ? AND dwz_grenze = ? AND published = ?")
												                                         ->limit(1)
												                                         ->execute($objSerie->id, $objTabellen->turnier, $platz_dwz, $gruppe['dwz_kategoriegrenze'], 1);
												
												//echo "<pre>";
												//echo 'Hauptpreis: ';
												//print_r($objHauptpreis->numRows); echo ' / Nebenpreis: ';
												//print_r($objNebenpreis->numRows); echo ' / DWZ: ';
												//print_r($Anmeldung['dwz']); echo ' / Grenze: ';
												//print_r($objNebenpreis->dwz_grenze); echo ' / Doppelpreise: ';
												//print_r($objSerie->doppelpreise); echo ' / Höhere Preise: ';
												//print_r($objSerie->hoeherepreise);
												//echo "</pre>";
												if($objHauptpreis->numRows && $objNebenpreis->numRows && $Anmeldung['dwz'] < $objNebenpreis->dwz_grenze)
												{
													// Hauptpreis vorhanden, Nebenpreis vorhanden, Anmelde-DWZ unterhalb DWZ-Grenze
													if($objSerie->doppelpreise)
													{
														// Doppelpreise erlaubt, Spieler bekommt beide Preise
														$tabelleArr[$zeile]['prices'][] = $objHauptpreis->id;
														$platz++;
														$tabelleArr[$zeile]['prices'][] = $objNebenpreis->id;
														$platz_dwz++;
													}
													elseif($objSerie->hoeherepreise)
													{
														// Nur ein Preis möglich, nämlich der höhere
														if($objHauptpreis->wert < $objNebenpreis->wert)
														{
															// Nebenpreis ist höherwertiger als der Hauptpreis
															$tabelleArr[$zeile]['prices'][] = $objNebenpreis->id;
															$platz_dwz++;
														}
														else
														{
															// Hauptpreis nehmen, da Nebenpreis nicht höherwertiger
															$tabelleArr[$zeile]['prices'][] = $objHauptpreis->id;
															$platz++;
														}
													}
													else
													{
														// Hauptpreis nehmen
														$tabelleArr[$zeile]['prices'][] = $objHauptpreis->id;
														$platz++;
													}
												}
												elseif(!$objHauptpreis->numRows && $objNebenpreis->numRows && $Anmeldung['dwz'] < $objNebenpreis->dwz_grenze)
												{
													// Hauptpreis nicht vorhanden, Nebenpreis vorhanden, Anmelde-DWZ unterhalb DWZ-Grenze
													$tabelleArr[$zeile]['prices'][] = $objNebenpreis->id;
													$platz_dwz++;
												}
												elseif($objHauptpreis->numRows)
												{
													// Hauptpreis vorhanden, Nebenpreis zu vernachlässigen
													$tabelleArr[$zeile]['prices'][] = $objHauptpreis->id;
													$platz++;
												}
											}
										}
									}
                    	
									//echo "<pre>";
									//print_r($tabelleArr);
									//echo "</pre>";
									// Tabelle aktualisieren
									$set = array
									(
										'importArray' => serialize($tabelleArr)
									);
									$objDB = \Database::getInstance()->prepare("UPDATE tl_internetschach_tabellen %s WHERE id = ?")
									                                 ->set($set)
									                                 ->execute($objTabellen->id);
								}
							}
						}
					}
				}
			}
		}

		// Zurück zur Seite
		\Controller::redirect(str_replace('&key=preise', '', \Environment::get('request')));
	}

}
