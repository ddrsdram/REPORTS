<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 10.02.2022
 * Time: 18:39
 */


session_start();
header('Content-Type: text/html; charset=UTF-8');
mb_http_output('UTF-8');

require "spl_autoload_register.php";
//require "backend/Connection.php";
$conn = new \backend\Connection(\properties\security::GD);
$job = true;
$countColl = 0;
while ($job){
    $isTheModemFree = $conn->table("SMS_manage")->select("session")->fetchField('session');
    $_uuid = sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));

    if ($isTheModemFree == "0"){
        $conn->table("SMS_manage")
            ->where('session',"0")
            ->set("session",$_uuid)
            ->update();
        $isTheModemFree = $conn->table("SMS_manage")->select("session")->fetchField('session');
        if ($isTheModemFree == $_uuid){
            $command = $conn->table("") ->complexQuery("update SMS_manage set lastDate=getdate()");

            $server = new \models\Server();
            $server->SendSMS();
            unset($server);

            $job = false;
        }
    }
    if (!$job){
        $conn->table("SMS_manage")
            ->set('session',"0")
            ->where("session",$_uuid)
            ->update();
    }else{

        sleep(2);
        $countColl ++;
        if ($countColl > 27) $job = false;
    }
}
