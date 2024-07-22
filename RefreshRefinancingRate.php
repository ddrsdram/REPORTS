<?php
require_once "spl_autoload_register.php";

if (count($argv) == 1){
    $date = date("d.m.Y");
}else{
    $date = $argv[1];
}
$RefinancingRate = new \models\RefinancingRate();
$RefinancingRate->setDateEnd($date);
$RefinancingRate->update();
print "OK";