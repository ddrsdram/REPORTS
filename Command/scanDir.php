<?php
require_once "../spl_autoload_register.php";
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL  );

$SD= new models\scanAllReports();
$SD->registerAllElements();
\Views\mPrint::R("выполнилось всё");
