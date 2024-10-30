<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');
mb_http_output('UTF-8');

require "spl_autoload_register.php";
print "Start</br>";

$query = "UPDATE S_B_K_CurentMonth set id_month = id_month + 1";
$conn = new \backend\Connection(\properties\security::GD);
$data = $conn->complexQuery($query);