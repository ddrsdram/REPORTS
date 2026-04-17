<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */


namespace Reports\Device\ExportDeviceForSBER;


use DB\Connection;
use DB\View\View_deviceForExportSBER;

class VIEW extends \Reports\reportView
{

    private $dataDev;

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

    /**
     * @param mixed $dataDev
     */
    public function setDataDev($dataDev): void
    {
        $this->dataDev = $dataDev;
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
                // Это придумано для того, чтобы сравнивать с целочисленными занчениями, т.к. с сервера все приходит в тексте

                $summa = str_replace('.', ",", $summa);
                $name = $A['name']; // регион
                $status_street = "{$A['status_street']}.";
                $street = $A['UL']; //status_street
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
                $listDev = $this->getStringDevByaLS($LS);
                $str = "$LS;$FIO;$id_LS_in_GISJKH;$HOUSEGUID_FIAS;$name, $status_street $street, $house, $room;$listDev";
                $str = iconv('UTF-8', 'windows-1251', $str);
                fwrite($db, $str . chr(13) . chr(10));
        }

        fclose($db);
    }

    public function getStringDevByaLS($id_LS)
    {
        $arr = $this->dataDev[$id_LS];
        $str = "";
        $d = new View_deviceForExportSBER();
        foreach ($arr as $key => $item){
            $id = $item[$d::id];
            $typA = $item[$d::typeAccrual];
            $dS = date("d.m.Y",strtotime($item[$d::date_sealing]));
            $name = $item[$d::name];
            $val = $item[$d::value_end];
            //            1     2   34   5    67
            $str = $str. "$typA;$id;;$dS;$val;;$name;";
        }

        return $str;
    }


}