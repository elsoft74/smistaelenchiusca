<?php
    include_once("../config/config.php");
    class DB {       
        public static function conn(){
            $msg="ERRORE DI CONNESSIONE";
            try {
                $dsn = 'mysql:host='.SERVER.';dbname='.DBNAME;
                $options = array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                ); 
                $conn = new PDO($dsn, UNAME, PASSWORD, $options);
                $msg="CONNESSO";
            } catch(Exception $e){
                $conn=null;
            }
            //file_put_contents("../log/dbtest.log",(new DateTime("now"))->format("Y-m-d H:i").$msg."\n",FILE_APPEND);
            return $conn;
        }

        public static function getUscaFromLocalita($localita) {
            $out = new stdClass();
            $out->status="KO";
            $out->data="";
            $conn = DB::conn();
            if ($conn != null){
                try {
                    $query = "SELECT chiave FROM `localita` JOIN `usca` ON `usca`.id=`localita`.`id_usca` WHERE `localita`.`nome` = UPPER(:localita)";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':localita',$localita,PDO::PARAM_STR);
                    $stmt->execute();
                    $res=$stmt->fetch(PDO::FETCH_ASSOC);
                    //var_dump($res);
                    $out->data=$res['chiave'];
                    $out->status="OK";
                } catch(Exception $ex){
                        $out->error=$ex->getMessage();
                    }
            }
            return $out;
        }
    }
?>
