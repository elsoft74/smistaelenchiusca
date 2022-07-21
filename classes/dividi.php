<?php
    require '../vendor/autoload.php';
    require_once("db.php");
    ini_set('memory_limit', '512M');
    use PhpOffice\PhpSpreadsheet\IOFactory;
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    
    Class Dividi{
        static function elabora($fileTmpLoc){
            //echo("elabora");
            //echo($fileTmpLoc);
            if(null!=$fileTmpLoc){
                $barcellona = Dividi::inizializza();
                $barcellonaArr = [];
                $lipari = Dividi::inizializza();
                $lipariArr = [];
                $messina = Dividi::inizializza();
                $messinaArr = [];
                $milazzo = Dividi::inizializza();
                $milazzoArr = [];
                $milazzoBarcellona = Dividi::inizializza();
                $milazzoBarcellonaArr = [];
                $mistretta = Dividi::inizializza();
                $mistrettaArr = [];
                $patti = Dividi::inizializza();
                $pattiArr = [];
                $roccalumera = Dividi::inizializza();
                $roccalumeraArr = [];
                $santagata = Dividi::inizializza();
                $santagataArr = [];
                $saponara = Dividi::inizializza();
                $saponaraArr = [];
                $taormina = Dividi::inizializza();
                $taorminaArr = [];
                
                $altri = Dividi::inizializza();
                $altriArr = [];

                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($fileTmpLoc);
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($fileTmpLoc);
                $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
                array_shift($sheetData);
                foreach ($sheetData as $row){
                    //var_dump($row);
                    if('Tampone a 7 giorni'==$row['J']){
                        if(!is_string($row['I'])){
                            $row['J']=7+$row['I'];
                        } else {
                            $row['J']=((DateTime::createFromFormat("d/m/Y",$row['I']))->add(new DateInterval('P7D')))->format("d/m/Y");
                        }
                    }
                    if('Tampone a 10 giorni'==$row['J']){
                        if(!is_string($row['I'])){
                            $row['J']=10+$row['I'];
                        } else {
                            $row['J']=((DateTime::createFromFormat("d/m/Y",$row['I']))->add(new DateInterval('P10D')))->format("d/m/Y");
                        }
                    }
                    $dom = Dividi::getUsca($row['G']);
                    switch($dom){
                        case ('BARCELLONA'):
                            array_push($barcellonaArr,$row);
                            break;
                        case ('LIPARI'):
                            array_push($lipariArr,$row);
                            break;
                        case ('MESSINA'):
                            array_push($messinaArr,$row);
                            break;
                        case ('MILAZZO'):
                            array_push($milazzoArr,$row);
                            break;
                        case ('MILAZZOBARCELLONA'):
                            array_push($milazzoBarcellonaArr,$row);
                            break;
                        case ('MISTRETTA'):
                            array_push($mistrettaArr,$row);
                            break;
                        case ('PATTI'):
                            array_push($pattiArr,$row);
                            break;
                        case ('ROCCALUMERA'):
                            array_push($roccalumeraArr,$row);
                            break;
                        case ('SANTAGATA'):
                            array_push($santagataArr,$row);
                            break;
                        case ('SAPONARA'):
                            array_push($saponaraArr,$row);
                            break;
                        case ('TAORMINA'):
                            array_push($taorminaArr,$row);
                            break;
                        default:
                            array_push($altriArr,$row);
                    }
                }

                Dividi::controllaSalva($barcellona,$barcellonaArr,"Barcellona");
                Dividi::controllaSalva($lipari,$lipariArr,"Lipari");
                Dividi::controllaSalva($messina,$messinaArr,"Messina");
                Dividi::controllaSalva($milazzo,$milazzoArr,"Milazzon");
                Dividi::controllaSalva($milazzoBarcellona,$milazzoBarcellonaArr,"MilazzoBarcellona");
                Dividi::controllaSalva($mistretta,$mistrettaArr,"Mistretta");
                Dividi::controllaSalva($patti,$pattiArr,"Patti");
                Dividi::controllaSalva($roccalumera,$roccalumeraArr,"Roccalumera");
                Dividi::controllaSalva($santagata,$santagataArr,"SantAgata");
                Dividi::controllaSalva($saponara,$saponaraArr,"Saponara");
                Dividi::controllaSalva($taormina,$taorminaArr,"Taormina");
                Dividi::controllaSalva($altri,$altriArr,"Altri");

            }   
        }

        static function inizializza(){
            $spreadsheet = new Spreadsheet();
            $spreadsheet->setActiveSheetIndex(0);
            $spreadsheet->getActiveSheet()->setCellValue('A1', '')
                ->setCellValue('B1', 'COGNOME')
                ->setCellValue('C1', 'NOME')
                ->setCellValue('D1', 'CODICE FISCALE')
                ->setCellValue('E1', 'DATA NASCITA')
                ->setCellValue('F1', 'CELLULARE')
                ->setCellValue('G1', 'DOMICILIO')
                ->setCellValue('H1', 'INDIRIZZO DOMICILIO')
                ->setCellValue('I1', 'DATA TAMPONE')
                ->setCellValue('J1', 'Giorno Tampone')
                ->setCellValue('K1', 'Vax cell')
                ->setCellValue('L1', 'Vax mail');
            return $spreadsheet;
        }

        static function salva($file,$usca){
            $now=new DateTime();
            $file->getActiveSheet()->getStyle('E:E')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
            $file->getActiveSheet()->getStyle('I:I')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
            $file->getActiveSheet()->getStyle('J:J')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($file);
            $filename = $now->format("YmdHi")."_".$usca.".xlsx";
            $pathAndName="../output/".$filename;
            $writer->save($pathAndName);
            
            try {
                $email = new PHPMailer();
$email->SetFrom('piattaformeinformatiche.covid@asp.messina.it', 'Piattaforme Informatiche'); //Name is optional
$email->Subject   = 'Tamponi';
$email->Body      = "In allegato i tamponi odierni.";
$email->AddAddress( 'piattaformeinformatiche.covid@asp.messina.it' );
$email->AddAttachment( $pathAndName , $filename );
$email->Send();
            } catch(Exception $ex){
            }
            

           
        }

        static function getUsca($val){
            $out = (DB::getUscaFromLocalita($val))->data;
            return $out;
        }

        static function controllaSalva($sheet,$array,$label){
            if ($array){
                $sheet->getActiveSheet()->fromArray($array, null, 'A2');
                Dividi::salva($sheet,$label);
            }
        }
    }
