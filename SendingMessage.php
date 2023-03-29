<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 22.09.2021
 * Time: 0:13
 */

/*
session_start();
header('Content-Type: text/html; charset=UTF-8');
mb_http_output('UTF-8');

require "spl_autoload_register.php";
print "Start</br>";
$conn = new \backend\Connection(backend\Connection::GD);

$data = $conn->table('View_S_B_K_SendingMessage')
    ->select();

while ($res = $data->fetch()){

    $nc = new \models\NameCaseLib\NCLNameCaseRu();
    $fio = $res['IO'];
    $MSG = new \Views\HTML\Msg_missedPayment($fio);
    $MSG->setContractDate( $res['Contract_date']);
    $MSG->setContractNum( $res['Contract_num']);
    $MSG->setSum( round($res['invoicedAamount'],2));
    $MSG->setNameAIS( $res['name_AIS']);
    $message = $MSG->getMessage();
    print $message;

    $mail = new \models\SendMail();
    $to = $res['eMail'];
    //$to = 'sediant2021@mail.ru';
    $mail->setAddress($to);
    $mail->setSubject('АИС СеДиАнт - Автоматезированное оповещение');
    $mail->setContent($message);
    $mail->send();
    unset($mail);
}

print "End</br>";
*/