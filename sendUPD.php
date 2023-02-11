<?php
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

require "spl_autoload_register.php";
print "Start</br>";
$conn = new \backend\Connection(\backend\Connection::GD);
$conn_update = new \backend\Connection(\backend\Connection::GD);

$addDocument = false;
$sendMailMyOnly = true;

$data = $conn->complexQuery("
SELECT        TOP (100) PERCENT dbo.S_B_K_UniversalPaymentDocument.ORG, dbo.S_B_K_UniversalPaymentDocument.id_month, dbo.S_B_K_UniversalPaymentDocument.id, dbo.S_B_K_UniversalPaymentDocument.invoicedAamount, 
                         dbo.S_B_K_E_MailAddress.eMail, dbo.S_B_K_E_MailAddress.IO, dbo.ORG.name_AIS, dbo.ORG.Contract_num, dbo.ORG.Contract_date, dbo.users.id AS id_user
FROM            dbo.S_B_K_UniversalPaymentDocument INNER JOIN
                             (SELECT        TOP (100) PERCENT MAX(id_month) AS id_month, ORG
                               FROM            dbo.S_B_K_UniversalPaymentDocument AS S_B_K_UniversalPaymentDocument_1
                               GROUP BY ORG) AS S_B_K_CurentMonth ON dbo.S_B_K_UniversalPaymentDocument.id_month = S_B_K_CurentMonth.id_month AND dbo.S_B_K_UniversalPaymentDocument.ORG = S_B_K_CurentMonth.ORG INNER JOIN
                         dbo.S_B_K_E_MailAddress ON dbo.S_B_K_UniversalPaymentDocument.ORG = dbo.S_B_K_E_MailAddress.ORG INNER JOIN
                         dbo.ORG ON dbo.S_B_K_UniversalPaymentDocument.ORG = dbo.ORG.ORG INNER JOIN
                         dbo.users ON dbo.ORG.ORG = dbo.users.ORG
WHERE        (dbo.S_B_K_E_MailAddress.send_PRO = 1) AND (dbo.users.login = 'SERVER')
");

while ($res = $data->fetch()){
    $_SESSION['id_month0'] = $res['id_month'];
    $_SESSION['ORG'] = $res['ORG'];
    $_SESSION['id_user'] = $res['id_user'];
    // Сформировать файл с по
    $res['Contract_date'] = date('d.m.Y',strtotime($res['Contract_date']));
    $res['invoicedAamount'] = round($res['invoicedAamount'],2);
    $UPD = new \models\UPD();
    $UPD->setIdUPD($res['id']);
    $UPD->setORG($res['ORG']);
    $UPD->setSum($res['invoicedAamount']);
    $UPD->setNameAIS($res['name_AIS']);
    $UPD->setContractNum($res['Contract_num']);
    $UPD->setContractDate($res['Contract_date']);
    $filename  = $UPD->create();
    unlink(__DIR__."/download/Универсальный_передаточный_документ.pdf");
    rename(__DIR__."/download/$filename",__DIR__."/download/Универсальный_передаточный_документ.pdf");


    $fio =  $res['IO'];
    $MSG = new \Views\HTML\Msg_sendUPD($fio);
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
    $mail->setAttachFile(__DIR__."/download/Универсальный_передаточный_документ.pdf");
    $mail->send();
    unset($mail);


}

print "End</br>";