<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 03.09.2020
 * Time: 21:31
 */

spl_autoload_register(function ($class) {
    $path = explode("\\", $class);
    $class = __DIR__;
    $_SESSION['__DIR__']  = __DIR__;
    //print chr(13).chr(10)."</br> --$class-- </br>".chr(13).chr(10);
    if (strlen($class) == 0) $class = "/var/www/html";
//    print chr(13).chr(10)."</br> --$class-- </br>".chr(13).chr(10);
    //$path = array_unique($path);
    foreach ($path as $key => $value) {
        $rep = false;
        switch ($value) {
            case "PhpOffice":
            case "Zend":
            case "Psr":
            case "PHPCadesSOAP":
            case "ZipStream":
            case "MyCLabs":
                if ($key == 0)
                    $value = "External\\" . $value;
                break;
        }
        $class = $class . "\\" . $value;
    }
    if ($rep) \models\ErrorLog::saveError($path,'foundClass.txt');

    $class = $class . '.php';
    $class = str_replace("\\", "/", $class);
  //  print $class;
    //  print chr(13).chr(10)."</br> $class </br>".chr(13).chr(10);
    if (file_exists($class)) {
        require_once $class;
        if ($rep) \models\ErrorLog::saveError("Found class $class",'foundClass.txt');
    }else{
        if ($rep) \models\ErrorLog::saveError("NO Found class $class",'foundClass.txt');

    }
});


