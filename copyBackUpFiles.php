<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 26.02.2022
 * Time: 16:29
 */
set_time_limit (0);
require "spl_autoload_register.php";

print date ("Y-m-d H:i:s");
print "</br>";

$security = new \properties\security();
$ftp = new \models\ftp();
$ftp->setHost($security->getBackupFtpServer());
$ftp->setLogin($security->getBackupFtpUser());
$ftp->setPass($security->getBackupFtpPassword());
$ftp->connection();
$ftp->setDirSource('');
$ftp->setDirDestination('/hdd2');
$ftp->download();
$message = "Скопированные файлы </br>".chr(10).chr(13) . $ftp->getFileRegistry();
$subject = "АИС СеДиАнт - Оповещение о резервном копировании";

$mail = new \models\SendMail();
$mail->setAddress('tehnosd@mail.ru');

$mail->setSubject($subject);
$mail->setContent($message);
$mail->send();
unset($mail);

print $message;

print date ("Y-m-d H:i:s");

$d = dir("/hdd2");
echo "Дескриптор: " . $d->handle . "\n";
echo "Путь: " . $d->path . "\n";
$date1 = new DateTime(date("d.m.Y"));

while (false !== ($entry = $d->read())) {
    if ( ! ( ( $entry == "." ) || ( $entry == ".." ) ) ){
        $filename = $d->path . '/' . $entry;

        echo $filename."\n";
        if (file_exists($filename)) {
            $file_creation_date = filectime($filename);
            $date2 = new DateTime(date('d.m.Y', $file_creation_date));
            $interval = $date1->diff($date2);
            print_r($interval). "\n";
            if ($interval->days > 35){
                unlink($filename);
            }
        } else {
            echo "File does not exist."."\n";
        }
    }


}
$d->close();