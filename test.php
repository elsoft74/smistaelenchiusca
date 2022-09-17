<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// use PHPMailer\PHPMailer\PHPMailer;
//     use PHPMailer\PHPMailer\Exception;
//     use PHPMailer\PHPMailer\SMTP;
//     use PHPMailer\PHPMailer\IMAP;
    include_once("config/config.php");

    $path = "{".SENDERSERVER.":".SENDERPORT."}"; // [INBOX]/Sent Mail
    $imapStream = imap_open($path, SENDERUSERNAME, SENDERPASSWORD);
    var_dump($imapStream);
    imap_close($imapStream);
    

//     $folders = imap_listmailbox($imapStream, $path, "*");

// if ($folders == false) {
//     echo "Call failed<br />\n";
// } else {
//     foreach ($folders as $val) {
//         echo $val . "<br />\n";
//     }
// }

// phpinfo();

?>