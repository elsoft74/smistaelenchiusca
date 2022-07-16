<?php
    // require __DIR__ . '/vendor/autoload.php';
    require '../vendor/autoload.php';
    use PhpOffice\PhpSpreadsheet\IOFactory;
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    Class Dividi{
        static function elabora($fileTmpLoc){
            $messina = Dividi::inizializza();
            $messinaArr = [];
            $altri = Dividi::inizializza();
            $altriArr = [];
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($fileTmpLoc);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($fileTmpLoc);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            array_shift($sheetData);
            foreach ($sheetData as $row){
                if('Tampone a 7 giorni'==$row['M']){
                    $row['M']=7+$row['I'];
                }
                if('Tampone a 10 giorni'==$row['M']){
                    $row['M']=10+$row['I'];
                }
                switch($row['G']){
                    case ('MESSINA (ME)'):
                        array_push($messinaArr,$row);
                        break;
                    default:
                        array_push($altriArr,$row);
                }
            }
            $messina->getActiveSheet()->fromArray($messinaArr, null, 'A2');
            $altri->getActiveSheet()->fromArray($altriArr, null, 'A2');
            Dividi::salva($messina,"Messina");
            Dividi::salva($altri,"Altri");
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
    }