<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\Recalculation\HOT_autoRecalc;


use DB\View\View_GHR_autoCalc_svod;

class MODEL extends \Reports\reportModel
{
    public function getDataTable($headArray)
    {
        $id_month = $headArray['id_Month_RecalculationForREP'];
        $conn = new \backend\Connection();
        $retArr = Array();
        $d = new View_GHR_autoCalc_svod();
        $dataArr = $d
            ->where($d::ORG,$this->getORG())
            ->where($d::id_user,$this->getUser())
            ->where($d::id_month_create,$id_month)
            ->orderBy("status_street,name_street,house_int,house,id_month")
            ->select()->fetchAll();
        $addres_house = false;
        $table = Array();
        foreach ($dataArr as $key => $rowArray){
            $row_addres_house = "{$rowArray[$d::name_region]} {$rowArray[$d::status_street]} {$rowArray[$d::name_street]}, д.{$rowArray[$d::house]}";
            if ($addres_house === false) {
                $addres_house = $row_addres_house;
                $name_street = "{$rowArray['status_street']} {$rowArray['name_street']}";
                $house = $rowArray['house'];
            }

            if ($addres_house != $row_addres_house){
                $retArr[] = array('addres_house'=> $addres_house,
                                    'name_street'=> $name_street,
                                    'house'=> $house,
                                    'table1'=>$table);
                $addres_house = $row_addres_house;
                $table = Array();
            }
            $table[] = $rowArray;
        }
        //добавляем последние собранные данные
        $retArr[] = array('addres_house'=> $addres_house,
            'name_street'=> $name_street,
            'house'=> $house,
            'table1'=>$table);
        $addres_house = $row_addres_house;
        return $retArr;

    }

}