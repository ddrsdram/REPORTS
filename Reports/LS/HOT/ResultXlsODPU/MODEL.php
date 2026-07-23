<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\LS\HOT\ResultXlsODPU;


use DB\Connection;
use DB\Table\device_HOT_xls;
use DB\Table\device_HOT_xls_byRow;
use properties\security;

class MODEL extends \Reports\reportModel
{
    private $id;
    public function saveFile($saveAsName)
    {
        $conn = new \DB\Connect();
        $headData = $this->getHeadArray();
        $id = $headData['ODPU_id_Xls'];

        $this->id = $id;

        $d = new device_HOT_xls();
        $path = security::DIR;
        $data = $d->where($d::id,$id)->select($d::bodyFileStart)->fetchField($d::bodyFileStart);
        $file = fopen("$path/ImpExp/".$saveAsName, "w+");
        fwrite($file, $data);
        fclose($file);
    }

    public function getListExclude()
    {
        $d = new device_HOT_xls_byRow();
        return $d->where($d::id_xls,$this->id)
            ->where($d::f_excludeRow,"1")
            ->select()->fetchAll();
    }

    public function getListFix()
    {
        $d = new device_HOT_xls_byRow();
        return $d->where($d::id_xls,$this->id)
            ->where($d::f_fixRow,"1")
            ->select()->fetchAll();
    }

    public function getTotal()
    {
        $d = new device_HOT_xls();
        return $d->where($d::id,$this->id)->select($d::value)->fetchField($d::value);
    }
}