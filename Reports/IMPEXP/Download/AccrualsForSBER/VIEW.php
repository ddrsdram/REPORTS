<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */


namespace Reports\IMPEXP\Download\AccrualsForSBER;



class VIEW extends \Reports\reportView
{

    /**
     * @var \backend\Connection
     */


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
        }

        $db = fopen($this->resultFilePath.'/'.$this->nameReport.$this->extensionName, 'w+');
        $AllSumma = 0;
        $kol = 0;
        foreach ($arr as $key => $A){
            // Это придумано для того, чтобы сравнивать с целочисленными занчениями, т.к. с сервера все приходит в тексте
            $summa = (int) ($A['Saldo_END'] * 100);
            $summaNach = $A['NACH'] * 100;

            if ($summa <= 0){
                $summa = $summa * -1;
                if ($summaNach > 0){ // если сальдо меньше нуля а сумма начисления Больше нуля
                    $x = (int) round(($summa * 100 / $summaNach),0);
                    // если процент переплаты составляет более 10% то выставляем нулевую сумму
                    if ($x <= 10)
                        $summa = $summaNach;
                    else
                        $summa = 0;
                }else{
                    $summa = 0; // если сальдо меньше нуля и сумма начисления нулевая, то в файл ставим ноль
                }
            }


            if ($summa >= 0 ){  // если не минус то сдобавляем строку
                // Это придумано для того, чтобы сравнивать с целочисленными занчениями, т.к. с сервера все приходит в тексте
                $name = $A['name']; // регион
                $street = $A['UL'];
                $house = $A['DOM'];
                $room = $A['KV'];
                $FIO = $A['FIO'];
                $LS = $A['LCHET'];
                $mes = str_pad($A['MES'], 2, '0', STR_PAD_LEFT);;
                $year = $A['GOD'];

                $AllSumma += $summa;
                $kol++;
                $str = "$LS|$FIO|$name, $street, $house, $room|1|Оплата за квартиру|$mes$year||$summa";
                $str = iconv('UTF-8', 'windows-1251', $str);
                fwrite($db, $str . chr(13) . chr(10));
            }
        }
        fwrite ( $db, "=|$kol|$AllSumma");

        fclose($db);
    }




}