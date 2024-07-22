<?php
require_once "spl_autoload_register.php";


set_time_limit(0);


$RefinancingRate = new \models\RefinancingRate();
if (count($argv) == 1){

}else{
    $date = $argv[1];
    $RefinancingRate->setDateStart($argv[1]);
}
print $date;

$RefinancingRate->setDateEnd(date("d.m.Y"));
$RefinancingRate->update();
print "OK";