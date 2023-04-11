<?php

namespace models;

use properties\security;

class ErrorLog
{
    static function saveError($value, $fileName = 'MyErrorLog.txt',$typeSaveMode = "a")
    {
        ob_start();
        print_r($value);
        $out = ob_get_contents();
        ob_end_clean();

//        $file = fopen($_SERVER['DOCUMENT_ROOT']."/".$fileName, $typeSaveMode);
        //$path = $_SESSION['__DIR__'];
        $path = security::DIR;
        $file = fopen("$path/log/".$fileName, $typeSaveMode);

        fwrite($file, $out . "\r\n");

        fclose($file);
    }
}
