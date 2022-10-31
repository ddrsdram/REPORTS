<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */


namespace Reports\IMPEXP\Download\AccrualsForMail;



class VIEW extends \Reports\reportView
{

    function __construct()
    {
        $path = $_SERVER['DOCUMENT_ROOT'];
        $this->resultFilePath = $path."/ImpExp";
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    public function fillInFile()
    {
        $arr = Array();;
        $summa = 0;
        while ($res = $this->data->fetch()){
            $res['SALDON'] = $res['SALDON'] * 1;
            $arr[] = $res;

            $summa += $res['SALDON'];
        }

        $db = fopen($this->resultFilePath.'/'.$this->nameReport.$this->extensionName, 'w+');
        $AllSumma = 0;
        $kol = 0;
        foreach ($arr as $key => $A){
            $summa = $A['Saldo_END'];
            //$summa = $summa = str_replace('.',',',$summa);
            $name = $A['name']; // регион
            $street = $A['UL'];
            $house = $A['DOM'];
            $room = $A['KV'];
            $FIO = $A['FIO'];
            $LS = $A['LCHET'];
            $AllSumma += $summa;
            $kol ++;
            $period = sprintf('%02d',date('m')).sprintf('%02d',date('y'));

            $str = "$LS;;$name, $street, $house, $room;$period;$summa";
            $str =  iconv('UTF-8', 'windows-1251', $str);
            fwrite ( $db, $str.chr(13).chr(10) );
        }
        //fwrite ( $db, "=|$kol|$AllSumma");

        fclose($db);
    }




}