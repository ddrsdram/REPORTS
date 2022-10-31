<?php
$asd=4;
error_reporting(E_ALL );

session_start();
require_once "spl_autoload_register.php";

$app = new \models\Router();
if (isset($argc)){
    $app->setArgumentCount($argc);
    $app->setArgumentArray($argv);
}

$app->AppRun();
