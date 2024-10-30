<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 22.09.2021
 * Time: 0:13
 */


session_start();
header('Content-Type: text/html; charset=UTF-8');
mb_http_output('UTF-8');

require "spl_autoload_register.php";
print "Start</br>";
$conn = new \backend\Connection(\properties\security::GD);

$connUpdate = new \backend\Connection(\properties\security::GD);

/*
  получаем все имена организаций
 */
$arrNamesORG = $conn->table('S_B_K_toWorkWithSMS')
    ->select()->fetchAll();

/*
  загружаем все непрочтённые SMS
*/
$data = $conn->table('SMS_BIG')
    ->where('sms_read',"0")
    ->orderBy('id')
    ->select("*");

while ($res = $data->fetch()){
    $text_SMS = $res['text'];
    $id_SMS = $res['id'];
    print "1111</br>";
    if (mb_substr($text_SMS,0,23)=="СберБизнес. Поступление"){


        foreach ($arrNamesORG as $key => $vArr){
            $name_ORG = $vArr['name_intoSMS'];
            $ORG = $vArr['ORG'];
            if (mb_strripos($text_SMS,$name_ORG,23,'utf-8')){
                print "ORG = {$ORG} </br>";
                $arrSum_1 = explode('р на р/c',$text_SMS);
                $text_summ = $arrSum_1[0];
                preg_match('/([0-9]+\.[0-9]+)/',$text_summ, $matches);
                $summa = $matches[0];
                print "SUMM = $summa </br>";
                $id_UPD = $connUpdate->table('S_B_K_SMS_forPayment')
                    ->where('ORG',$ORG)
                    ->select('id')->fetchField('id');
                $id_UPD = $connUpdate->table('S_B_K_UniversalPaymentDocument')
                    ->where('ORG',$ORG)
                    ->where('id',$id_UPD)
                    ->set('paidAmount',$summa)
                    ->update();
            }
        }
    }
    $connUpdate->table('SMS_BIG')
        ->set('sms_read',"1")
        ->where('id',$id_SMS)
        ->update();

}

print "End</br>";