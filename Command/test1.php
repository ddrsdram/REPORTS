<?php
$t1['V_UL'] = 1;
$R =1;

$val = "";



$val1 =  '$t1["V_UL"] = 1 ? "=L$R-I$R" : ""';


$command = '$val = '.$val1.';';
eval($command);
print $val;