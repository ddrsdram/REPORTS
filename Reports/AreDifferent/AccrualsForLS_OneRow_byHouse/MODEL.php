<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\AreDifferent\AccrualsForLS_OneRow_byHouse;


use DB\Connection;
use DB\View\View_AFY_accruals_byHouse;
use DB\View\View_AFY_head_byHouse;
use DB\View\View_AFY_usedTypeAccrual;

class MODEL extends \Reports\reportModel
{

    public function getDataArray()
    {
        $conn_table = new \backend\Connection();
        $ret = Array();

        $id_user = $this->getUser();

        $d = new View_AFY_head_byHouse();

        $data_head = $d
            ->where($d::id_user,$id_user)
            ->orderBy($d::name_street.",".$d::id_street.",".$d::int_house.",".$d::house)//'name_street,id,int_house,house'
            ->select();

        $d1 = new View_AFY_accruals_byHouse();
        $data_table = $d1
            ->where($d1::id_user,$id_user)
            ->where($d1::id_street,0,' <> ')
            ->orderBy($d1::name_street.",".$d1::id_street.",".$d1::int_house.",".$d1::house.",".$d1::sorting) //'id_street,int_house,house,sorting'
            ->select();


        while ($value = $data_head->fetch()){
            $index = $value['id_street'].'_'.$value['house'];
            $ret[$index] = $value;
        }
        while ($value = $data_table->fetch()){
            $index = $value['id_street'].'_'.$value['house'];
            $ret[$index]['table'][] = $value;
        }
        return $ret;
    }

    public function getTypeAccrual()
    {
        $d = new View_AFY_usedTypeAccrual();
        return  $d
            ->where($d::ORG,$this->getORG())
            ->where($d::id_user,$this->getUser())
            ->where($d::id,0,'<>')
            ->orderBy($d::sorting)
            ->select($d::name.",".$d::detailing_general_report);
    }
}