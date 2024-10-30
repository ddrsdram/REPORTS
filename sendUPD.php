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
print "Start".chr(10).chr(13);
print count($argv).chr(10).chr(13);

if (count($argv) == 1){
    print "Структура запроса".chr(10).chr(13);
    print "sendUPD.php <true-отпрвить письма только на мой ящик |false> [<ALL|2|3|4|5-номер организации> <207|208-порядковый номе месяца>]".chr(10).chr(13);
    return;
}
if (($argv[1] == 'true') || ($argv[1] == 'false')){
    $sendMailMyOnly =  $argv[1] == 'true' ? true : false;
    print "Первый параметр верный =$sendMailMyOnly=".chr(10).chr(13);
}else{
    print "Не ВЕРНЫЙ первый параметр ".chr(10).chr(13);
    return;
}

if ($argv[2] == 'ALL'){
    $query = "SELECT        S_B_K_UniversalPaymentDocument.ORG, S_B_K_UniversalPaymentDocument.id_month
                FROM            S_B_K_CurentMonth INNER JOIN
                         S_B_K_UniversalPaymentDocument ON S_B_K_CurentMonth.id_month = S_B_K_UniversalPaymentDocument.id_month";
}else{
    $ORG = $argv[2];
    $id_month = $argv[3];
    $query = "SELECT        ORG, id_month
                FROM            S_B_K_UniversalPaymentDocument
                where ORG = $ORG AND
                id_month = $id_month";

}
$conn = new \backend\Connection(\properties\security::GD);
$data = $conn->complexQuery($query);

$class = new \models\SendUPD();
while ($res = $data->fetch()){
    $class->setORG($res['ORG']);
    $class->setIdMonth($res['id_month']);
    $class->setSendMailMyOnly($sendMailMyOnly);
    $class->send();
}

print "End</br>";