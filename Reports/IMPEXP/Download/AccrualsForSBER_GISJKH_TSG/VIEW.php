<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */


namespace Reports\IMPEXP\Download\AccrualsForSBER_GISJKH_TSG;



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
        }

        $db = fopen($this->resultFilePath.'/'.$this->nameReport.$this->extensionName, 'w+');
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
                $summa = $summa / 100;

                $summa = str_replace('.', ",", $summa);
                $name = $A['name']; // регион
                $street = $A['UL'];
                $house = $A['DOM'];
                $room = $A['KV'];
                $FIO = $A['FIO'];
                $LS = $A['LCHET'];
                $mes = str_pad($A['MES'], 2, '0', STR_PAD_LEFT);;
                $year = substr($A['GOD'],2,2);
                $id_LS_in_GISJKH = $A['id_LS_in_GISJKH'];
                $roomForFIAS = $room == '' ? '' : ','.$room; // если есть квартира то добавляем к номеру по фиас
                $HOUSEGUID_FIAS = $A['HOUSEGUID_FIAS'] == '' ? '' : $A['HOUSEGUID_FIAS'].$roomForFIAS; // Если есть ФИАС то добавляем фиас с номером квартиры иначе пустое поле
                $kol++;
                $str = "$LS;$id_LS_in_GISJKH;$HOUSEGUID_FIAS;$FIO;$name $street $house $room;$mes$year;$summa";
                $str = iconv('UTF-8', 'windows-1251', $str);
                fwrite($db, $str . chr(13) . chr(10));
            }
        }

        fclose($db);
    }




}