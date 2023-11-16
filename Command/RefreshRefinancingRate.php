<?php
require_once "../spl_autoload_register.php";
$RefinancingRate = new \models\RefinancingRate();
$RefinancingRate->setDateEnd(date("d.m.Y"));
$RefinancingRate->update();
print "OK";