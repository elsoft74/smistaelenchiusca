<?php
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);
    include_once("../classes/dividi.php");
    $out = new StdClass();
    $out->status="KO";
    //$out->head=getallheaders();
    try{
        $fileName = $_FILES["file"]["name"]; 
        $fileTmpLoc = $_FILES["file"]["tmp_name"];
        $invia=array_key_exists("invia",$_POST)?$_POST["invia"]=="true":false;
        $cancella=array_key_exists("cancella",$_POST)?$_POST["cancella"]=="true":false;
        //var_dump($_POST);
        $out=Dividi::elabora($fileTmpLoc,$invia,$cancella);
        //$out->status="OK";
    } catch(Exception $ex){
        $out->error=$ex->getMessage();
        $out->debug=print_r($_FILES,false);
    }
    echo(json_encode($out));
?>