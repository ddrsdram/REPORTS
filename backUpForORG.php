<?php
/**
 * Рассылка резервных копий БД по организациям
 * User: rezzalbob
 * Date: 04.05.2022
 * Time: 12:16
 */
set_time_limit (0);

session_start();
header('Content-Type: text/html; charset=UTF-8');
mb_http_output('UTF-8');

require "spl_autoload_register.php";
print "Start</br>";

$conn0 = new \backend\Connection(\properties\security::GD);

$query = 'SELECT        TOP (100) PERCENT serverName, [dataBase], userName, password
FROM            ORG
GROUP BY serverName, [dataBase], userName, password';
$allConnection = $conn0->complexQuery($query);


/*
 *  пробегаем по всем соединениям к БД
*/
while ($connection = $allConnection->fetch()){


    $conn = new \backend\Connection($connection);
    $conn1 = new \backend\Connection($connection);

    $data  = $conn->table("View_backUpForORG")
        ->select();

    $_SESSION['id_user'] = "0";

    while ($res = $data->fetch()){
        $_SESSION['ORG'] = $res['ORG'];
        $_SESSION['id_month0'] = $res['f_SendBackUp'];


        $fio =  $res['im_director']." ".$res['ot_director'];
        $MSG = new \Views\HTML\Msg_backUpForORG($fio);
        $MSG->setSum(0);
        $MSG->setNameAIS($res['name_AIS']);
        $MSG->setContractNum($res['Contract_num']);
        $MSG->setContractDate($res['Contract_date']);
        $message = $MSG->getMessage();
        print $message;


        $backUp = new \models\backUpForORG\createAndSend();
        $backUp->setEMailSendBackUp($res['eMailSendBackUp']);
        $backUp->setMessage($message);
        $backUp->setConnectionArray($connection);
        $backUp->run();

        /* пометка для бакапа снята*/
        $conn1->table('block')->set('f_SendBackUp',0)->where('ORG',$res['ORG'])->update();

        unset($backUp);
    }
}

