<?php
    $out = new StdClass();
    $out->status="KO";
    try{
        $rawData = file_get_contents("php://input");
        $path="/var/www/html/smistaelenchiusca/output/out.xlsx";
        if (!$fp = fopen($path, "wb")) {
            throw new Exception("Non posso aprire il file di output: ".$path); 
        }
        if (fwrite($fp, $rawData) === FALSE) {
            throw new Exception("Non posso scrivere il file di output.");
        }
        $out->status="OK";
    } catch(Exception $ex){
        $out->debug=$ex->getMessage();
    }
    echo(json_encode($out));
?>