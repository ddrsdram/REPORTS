<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');
mb_http_output('UTF-8');

require "spl_autoload_register.php";
print "Start</br>";



if (count($argv) == 1){
    print "Структура запроса".chr(10).chr(13);
    print "MSG_MonthIsNotClosed.php <true-отпрвить письма только на мой ящик |false> ".chr(10).chr(13);
    return;
}
if (($argv[1] == 'true') || ($argv[1] == 'false')){
    $sendMailMyOnly =  $argv[1] == 'true' ? true : false;
    print "Первый параметр верный =$sendMailMyOnly=".chr(10).chr(13);
}else{
    print "Не ВЕРНЫЙ первый параметр ".chr(10).chr(13);
    return;
}

$conn = new \backend\Connection(\backend\Connection::GD);
$data = $conn->table('View_S_B_K_MonthIsNotClosed')
    ->select();

while ($res = $data->fetch()){

    $_SESSION['id_month0'] = $res['id_month'];
    $_SESSION['ORG'] = $res['ORG'];
    $_SESSION['id_user'] = '0';// пользователь нужен но не важен
    // Сформировать файл с по
    $res['Contract_date'] = date('d.m.Y',strtotime($res['Contract_date']));
    $res['invoicedAamount'] = round($res['invoicedAamount'],2);

    $fio =  $res['IO'];
    $MSG = new \Views\HTML\MSG_MonthIsNotClosed($fio);
    $MSG->setSum($res['invoicedAamount']);
    $MSG->setNameAIS($res['name_AIS']);
    $MSG->setContractNum($res['Contract_num']);
    $MSG->setContractDate($res['Contract_date']);
    $message = $MSG->getMessage();
    print $message;

    $subject = "АИС СеДиАнт - Автоматезированное оповещение";

    $mail = new \models\SendMail();
    $mail->setAddress('tehnosd@mail.ru');
    if ($sendMailMyOnly === false)
        $mail->setAddress($res['eMail']);
    $mail->setSubject($subject);
    $mail->setContent($message);
    $mail->send();
    unset($mail);
}
