<?php
use PHPMailer\SMTP;

/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 22.09.2021
 * Time: 0:13
 */
set_time_limit (0);

session_start();
header('Content-Type: text/html; charset=UTF-8');
mb_http_output('UTF-8');
/*
require "spl_autoload_register.php";
print "Start</br>";
$conn = new \backend\Connection(\backend\Connection::GD);

$data = $conn->table('View_S_B_K_SendingMessage')
    ->select();

$res = $data->fetch();
    while ($res = $data->fetch()){

        $fio = $res['IO'];
        $MSG = new \Views\HTML\Msg_LasDayPayment($fio);
        $MSG->setContractDate( $res['Contract_date']);
        $MSG->setContractNum( $res['Contract_num']);
        $MSG->setSum( round($res['invoicedAamount'],2));
        $MSG->setNameAIS( $res['name_AIS']);
        $message = $MSG->getMessage();
        print $message;
        print chr(10).chr(13);
        print '=================================================';
        print chr(10).chr(13);

        $mail = new \models\SendMail();
        $mail->setAddress( $res['eMail']);
        $mail->setAddress('tehnosd@mail.ru');
        $mail->setSubject('Автоматезированное оповещение');
        $mail->setContent($message);
        $mail->send();
        unset($mail);

}
*/
print " End</br>";