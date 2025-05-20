<?php
namespace Views;

class mPrint
{
    const RED = '31';
    const GREEN = '32';
    const YELLOW = '33';
    const BLUE = '34';
    const PINK = '35';
    const LIGHT_BLUE = '36';

    static function R($data,$color = '00')
    {
        $cli = PHP_SAPI == 'cli';
        if ($cli){
            echo "\033[01;{$color}m";
            print_r($data);
            echo" \033[0m";

        } else{
            print_r($data);

        }
        // поределение откуда вызван скрипт из консоли или из браузера
        $CR = $cli ? chr(10).chr(13): "</br>";
        print $CR;
    }

    static function PDO(\PDOException $e,string $query)
    {

        $trace = $e->getTrace();
        foreach ($trace as $key => $item){
            mPrint::R($item['file']."---------- line = ".$item['line'],mPrint::RED);
        }
        mPrint::R($e->errorInfo,mPrint::RED);
        mPrint::R($query,mPrint::PINK);

    }
}