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
        static function elabora($fileTmpLoc,$invia,$cancella){
            $out = new StdClass();
            $out->status = "KO";
            $out->data = new StdClass();
            try {
                if(null!=$fileTmpLoc){
                    $spreadsheets = [];
                    
                    $tmpObj = new StdClass();
                    $tmpObj->spread = Dividi::inizializza();
                    $tmpObj->spreadArray = [];
                    $spreadsheets['ALTRI']=$tmpObj;
                    $keys=[];
                    
                    $tmpObj = DB::getChiaviUsca();
                    if($tmpObj->status=="OK"){
                        $keys=$tmpObj->data;
                    } else {
                        throw new Exception("Impossibile recuperare le chiavi USCA");
                    }
                    
                    foreach ($keys as $key){
                        $tmpObj = new StdClass();
                        $tmpObj->spread = Dividi::inizializza();
                        $tmpObj->spreadArray = [];
                        $spreadsheets[$key]=$tmpObj;
                    }

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
                        if (array_key_exists($dom,$spreadsheets)){
                            array_push($spreadsheets[$dom]->spreadArray,$row);
                        } else {
                            array_push($spreadsheets['ALTRI']->spreadArray,$row); 
                        }
                    }
    
                    $out->data=[];
                    foreach ($keys as $key){
                        $label = DB::getUscaLabel($key);
                        if ($label->status=="OK"){
                            $out->data[$key]=Dividi::controllaSalva($spreadsheets[$key]->spread,$spreadsheets[$key]->spreadArray,$label->data,$invia,$cancella);
                        } else {
                            throw new Exception("Errore durante il recuper dell'etichetta per:".$key);
                        }
                    }
                    $out->data[$key]=Dividi::controllaSalva($spreadsheets["ALTRI"]->spread,$spreadsheets["ALTRI"]->spreadArray,"Altri",$invia,$cancella);
    
                    $out->status="OK";
                } else {
                    throw new Exception("Impossibile leggere il file");
                }

            } catch (Exception $ex){
                $out->error = $ex->getMessage();
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

        static function salva($file,$usca,$invia,$cancella){
            $out = "Non inviata";
            date_default_timezone_set("Etc/UTC");
            $now=new DateTime();
            $file->getActiveSheet()->getStyle('E:E')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
            $file->getActiveSheet()->getStyle('I:I')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
            $file->getActiveSheet()->getStyle('J:J')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
            $file->getActiveSheet()->freezePane('A2');
            $file->getActiveSheet()->getStyle("A:L")->getFont()->setSize(11);
            foreach(range('A','L') as $columnID) {
                $file->getActiveSheet()->getColumnDimension($columnID)
                    ->setAutoSize(true);
            }
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($file);
            $filename = $now->format("YmdHi")."_".$usca.".xlsx";
            $pathAndName="../output/".$filename;
            $writer->save($pathAndName);
            $email = new PHPMailer(true);
            if($invia){
                try {
                    //$email->SMTPDebug = SMTP::DEBUG_CONNECTION;                      //Enable verbose debug output
                    $email->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
                    $email->isSMTP();                                            //Send using SMTP
                    $email->Host       = SENDERSERVER;                     //Set the SMTP server to send through
                    $email->SMTPAuth   = true;                                   //Enable SMTP authentication
                    $email->Username   = SENDERUSERNAME;                     //SMTP username
                    $email->Password   = SENDERPASSWORD;                               //SMTP password
                    $email->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
                    $email->Port       = SENDERPORT; 
                    $email->SetFrom(SENDEREMAIL, SENDERNAME); //Name is optional
                    $email->Subject   = 'Tamponi '.$usca;
                    $email->Body      = "In allegato i tamponi odierni.";
                    $email->AddAddress( 'laboratori.covid@asp.messina.it' );
                    $email->AddBcc(BCCADDRESS);
                    $email->AddAttachment( $pathAndName , $filename );
                    if (!$email->send()){
                        $out = "Non inviata ".$email->ErrorInfo;//$ex->getMessage();
                    } else {
                        $out = "Inviata";
                        if($cancella){
                            shell_exec("rm -f ".$pathAndName);
                        }
                    }
                } catch(Exception $ex){
                    $out = "Non inviata ".$email->ErrorInfo;//$ex->getMessage();
                }
            }
            
            return $out;
        }

        static function getUsca($val){
            $out = (DB::getUscaFromLocalita($val))->data;
            return $out;
        }

        static function controllaSalva($sheet,$array,$label,$invia,$cancella){
            $out = "Non generato.";
            if ($array){
                $sheet->getActiveSheet()->fromArray($array, null, 'A2');
                $out = Dividi::salva($sheet,$label,$invia,$cancella);
            }
            return $out;
        }
    }
