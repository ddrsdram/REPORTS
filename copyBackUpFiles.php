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


$ftp = new \models\ftp();
$ftp->setHost('13.14.0.6');
$ftp->setLogin('backupmanager');
$ftp->setPass("xnqot73obxoq48w");
$ftp->connection();
$ftp->setDirSource('');
$ftp->setDirDestination('/hdd2');
$ftp->download();


print date ("Y-m-d H:i:s");