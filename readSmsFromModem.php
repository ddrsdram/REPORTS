<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 10.02.2022
 * Time: 18:39
 */
/* ************************************/

/**/
session_start();
header('Content-Type: text/html; charset=UTF-8');
mb_http_output('UTF-8');


require "spl_autoload_register.php";
//require "backend/Connection.php";
$conn = new \backend\Connection(\backend\Connection::GD);
$job = true;
$countColl = 0;
$countRead = 0;

/*
проверяем, если порт занят согласно регистрации в БД более 2 минут, что не может быть реальностью, значит какой то БАГ и мы освобождаем его для текущей сесии.
соответственно если в процессе чтения будет фатальная ошибка то порт останется занатым более чем на 2 минуты. при этом
в FatalErrDate будет зарегестирована дата время последенй разблокировки по таймауту,
в FatalErr хранится еденица (1).
Если в текущей сесии после разблокировки будет успешное считывание СМС в FatalErr запишеться (0).
*/
$conn->complexQuery("update SMS_manage set [session] = 0, FatalErrDate =getdate(), FatalErr = 1 where DATEDIFF(SECOND, lastDate, getdate()) > 120 AND [session] <> '0'");

while ($job){
    $_uuid = sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));


    // выяснсяем состояние занятости модема зарегестированного в БД
    $isTheModemFree = $conn->table("SMS_manage")->select("session")->fetchField('session');
    if ($isTheModemFree == "0"){ // если БД говорит, что модем не занят

        // записываем номер сесси и занимаем модем для работы в текущем скрипте
        $conn->table("SMS_manage")->where('session',"0")->set("session",$_uuid)->update();

        //проверяем удалось ли захватить занятость, считываем сессию из таблици и сравниваем со своей
        $isTheModemFree = $conn->table("SMS_manage")->select("session")->fetchField('session');
        if ($isTheModemFree == $_uuid){
            // помечаем поледнее вермя блокировки модема
            $command = $conn->table("") ->complexQuery("update SMS_manage set lastDate=getdate()");

            //считываение данных в БД
            $server = new \models\Server();
            $server ->readSMS();
            unset($server);

            // говорим что работа модема завершена
            $job = false;
            $countRead ++;
        }
    }
    if (!$job){ // Если чтение с модема произведено
        $conn->table("SMS_manage") //освобождаем его для других операций
            ->set('session',"0")
            ->set('FatalErr',"0")
            ->where("session",$_uuid)
            ->update();
        if ($countRead<=4) {
            sleep(12); // эжейм 12 секунд
            $job = true; // и повторяем чтение
        }
    }else{
        sleep(2); //через каждые 2 секунды проеряем освобождение порта
        $countColl ++;
        if ($countColl > 27) $job = false;
    }
}
