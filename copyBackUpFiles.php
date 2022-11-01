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


print date ("Y-m-d H:i:s");