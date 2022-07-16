<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    include_once("../classes/dividi.php");
    $out = new StdClass();
    $out->status="KO";
    //$out->head=getallheaders();
    try{
        $fileName = $_FILES["file"]["name"]; 
        $fileTmpLoc = $_FILES["file"]["tmp_name"];
        Dividi::elabora($fileTmpLoc);
        $out->status="OK";
    } catch(Exception $ex){
        $out->error=$ex->getMessage();
        $out->debug=print_r($_FILES,false);
    }
    echo(json_encode($out));
?>