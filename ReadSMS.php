<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 21.09.2021
 * Time: 16:04
 */


session_start();
header('Content-Type: text/html; charset=UTF-8');
mb_http_output('UTF-8');

require "spl_autoload_register.php";
print "Start</br>";
$conn = new \backend\Connection(\properties\security::GD);
$convertSMS = new \models\ConvertSMS(\properties\security::GD);
$connUpdate = new \backend\Connection(\properties\security::GD);
/*
  загружаем все непрочтённые SMS
*/
$data = $conn->table('View_SMS_receiving_sorting')
    //->where('sms_read',"0")
    //->where("SMS_telephone","None","<>")
    ->orderBy('uIdSMS, idSMS, colSMS')
    ->select("id,SMS_RAW");


$text_SMS = '';
$flag_saveSMS = false;
$id_SMS = 0;
while ($res = $data->fetch()){
    $SMS_RAW = $res['SMS_RAW'];
    $id = $res['id'];
    $convertSMS->setSMS($SMS_RAW);


    if ($convertSMS->detectQuantityThis() == 1 ){ // Если СМС Составная и получили певую часть
        $text_SMS = $convertSMS->SMS_text; //присваиваем общему тексту эту первую часть
        $id_SMS = $id; // запоминаем id для дальнейшей идентификации остальных из группы
        $flag_saveSMS = false; // и если уж СМС составная то она точно состоит не из одной части, значит не сохраняем её
    }


    if (($convertSMS->detectQuantityAll() > $convertSMS->detectQuantityThis()) &&
        ($convertSMS->detectQuantityThis() !=1 )){ // получая остальные части СМС но не первые
        $text_SMS .= $convertSMS->SMS_text; // добавляем текс к основному
        $flag_saveSMS = false; //и по прежнему не сохраняем её
    }
    if ($convertSMS->detectQuantityAll() == $convertSMS->detectQuantityThis()){ // Если мы достикли последнего элемента составной СМС
        $text_SMS .= $convertSMS->SMS_text; // дописываем его в основной текст
        $flag_saveSMS = true; // и разрешаем запись в новую таблицу SMS_BIG
    }
    if ($convertSMS->detectQuantityAll() == 0){ // Если СМС не составная
        $text_SMS = $convertSMS->SMS_text; // получаем текс
        $id_SMS = $id;// запоминаем id да идетификации
        $flag_saveSMS = true; // разрешаем запись в новую таблицу SMS_BIG
    }

    // помечаем записи с уже прочтенными СМС
    $connUpdate->table('SMS_receiving')
        ->set('sms_read_h',$id_SMS)
        ->where('id',$id)
        ->update();

    if ($flag_saveSMS == true){ // если готовы записать СМС

        // Запись готовой СМС
        $connUpdate->table('SMS_BIG')
            ->set('id_SMS_receiving',$id_SMS)
            ->set('text',$text_SMS)
            ->insert();
        // пометка как прочитанной в промежуточной таблице
        $connUpdate->table('SMS_receiving')
            ->set("sms_read","1")
            ->where('sms_read_h',$id_SMS)
            ->update();

        $text_SMS =''; // обнуление текса
        $id_SMS = 0; // однуление идетификации

        print "</br>";
    }
}
print "End</br>";