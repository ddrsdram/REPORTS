<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */


namespace Reports\IMPEXP\Download\ExportDevice_platosphere;



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
        $db = fopen($this->resultFilePath.'/'.$this->nameReport.$this->extensionName, 'w+');

        foreach ( $this->data as $key => $A){
            $id_LS = $A['id_LS'];
            $region = $A['name_region']; // регион

            $status_street = $A['status_street'];
            $name_street = $A['name_street'];
            $house = 'д.'.$A['house'];
            $room = $A['room'] == ''  ?  ''  :  ', кв.'.$A['room'];
            $id = $A['id'];
            $id_device_GISGKH = $A['id_device_GISGKH'];
            $name_device = $A['name_device'];
            $name_type_accrual = $A['name_type_accrual'];
            $value_end = $A['value_end'];

            $str = "$id_LS;$region, $status_street $name_street, $house$room;$id;$id_device_GISGKH;$name_device;$name_type_accrual;$value_end ";
            $str = iconv('UTF-8', 'windows-1251', $str);
            fwrite($db, $str . chr(13) . chr(10));
        }
        fclose($db);
    }




}