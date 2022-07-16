<?php
    require '../vendor/autoload.php';
    require_once("db.php");
    ini_set('memory_limit', '128M');
    use PhpOffice\PhpSpreadsheet\IOFactory;
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    
    Class Dividi{
        static function elabora($fileTmpLoc){
            //echo("elabora");
            //echo($fileTmpLoc);
            if(null!=$fileTmpLoc){
                $messina = Dividi::inizializza();
                $messinaArr = [];
                $altri = Dividi::inizializza();
                $altriArr = [];
                $taormina = Dividi::inizializza();
                $taorminaArr = [];
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($fileTmpLoc);
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($fileTmpLoc);
                $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
                array_shift($sheetData);
                foreach ($sheetData as $row){
                    //var_dump($row);
                    if('Tampone a 7 giorni'==$row['M']){
                        $row['M']=7+$row['I'];
                    }
                    if('Tampone a 10 giorni'==$row['M']){
                        $row['M']=10+$row['I'];
                    }
                    $dom = Dividi::getUsca($row['G']);
                    switch($dom){
                        case ('MESSINA'):
                            array_push($messinaArr,$row);
                            break;
                        case ('TAORMINA'):
                            array_push($taorminaArr,$row);
                            break;
                        default:
                            array_push($altriArr,$row);
                    }
                }
                $messina->getActiveSheet()->fromArray($messinaArr, null, 'A2');
                $taormina->getActiveSheet()->fromArray($taorminaArr, null, 'A2');
                $altri->getActiveSheet()->fromArray($altriArr, null, 'A2');
                Dividi::salva($messina,"Messina");
                Dividi::salva($taormina,"Taormina");
                Dividi::salva($altri,"Altri");
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
                ->setCellValue('J1', '')
                ->setCellValue('K1', '')
                ->setCellValue('L1', '')
                ->setCellValue('M1', 'Giorno Tampone')
                ->setCellValue('N1', 'Vax cell')
                ->setCellValue('O1', 'Vax mail');
            return $spreadsheet;
        }

        static function salva($file,$usca){
            $now=new DateTime();
            $file->getActiveSheet()->getStyle('I:I')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
            $file->getActiveSheet()->getStyle('M:M')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($file);
            $pathAndName="../output/".$now->format("YmdHi")."_".$usca.".xlsx";
            $writer->save($pathAndName);
        }

        static function getUsca($val){
            $out = (DB::getUscaFromLocalita($val))->data;
            return $out;
        }
    }