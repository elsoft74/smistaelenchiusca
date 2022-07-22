<?php
    require '../vendor/autoload.php';
    require_once("db.php");
    ini_set('memory_limit', '512M');
    use PhpOffice\PhpSpreadsheet\IOFactory;
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\SMTP;
    
    Class Dividi{
        static function elabora($fileTmpLoc){
            //echo("elabora");
            //echo($fileTmpLoc);
            $out = new StdClass();
            $out->status = "KO";
            $out->data = new StdClass();
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

                $out->data->barcellona=Dividi::controllaSalva($barcellona,$barcellonaArr,"Barcellona");
                $out->data->lipari=Dividi::controllaSalva($lipari,$lipariArr,"Lipari");
                $out->data->messina=Dividi::controllaSalva($messina,$messinaArr,"Messina");
                $out->data->milazzo=Dividi::controllaSalva($milazzo,$milazzoArr,"Milazzo");
                $out->data->milazzobarcellona=Dividi::controllaSalva($milazzoBarcellona,$milazzoBarcellonaArr,"MilazzoBarcellona");
                $out->data->mistretta=Dividi::controllaSalva($mistretta,$mistrettaArr,"Mistretta");
                $out->data->patti=Dividi::controllaSalva($patti,$pattiArr,"Patti");
                $out->data->roccalumera=Dividi::controllaSalva($roccalumera,$roccalumeraArr,"Roccalumera");
                $out->data->santagata=Dividi::controllaSalva($santagata,$santagataArr,"SantAgata");
                $out->data->saponara=Dividi::controllaSalva($saponara,$saponaraArr,"Saponara");
                $out->data->taormina=Dividi::controllaSalva($taormina,$taorminaArr,"Taormina");
                $out->data->altri=Dividi::controllaSalva($altri,$altriArr,"Altri");
                $out->status="OK";
            }
            return $out;   
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
                $email = new PHPMailer(true);
                //$email->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
                $email->isSMTP();                                            //Send using SMTP
                $email->Host       = SENDERSERVER;                     //Set the SMTP server to send through
                $email->SMTPAuth   = true;                                   //Enable SMTP authentication
                $email->Username   = SENDERUSERNAME;                     //SMTP username
                $email->Password   = SENDERPASSWORD;                               //SMTP password
                $email->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
                $email->Port       = SENDERPORT; 
                $email->SetFrom(SENDEREMAIL, SENDERNAME); //Name is optional
                $email->Subject   = 'Tamponi '.$filename;
                $email->Body      = "In allegato i tamponi odierni.";
                $email->AddAddress( 'piattaformeinformatiche.covid@asp.messina.it' );
                $email->AddAttachment( $pathAndName , $filename );
                $email->Send();
                return true;
            } catch(Exception $ex){
                return $ex->ErrorInfo();
            }
            return true;

           
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
