<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\Recalculation\HOT_autoRecalc;


class MODEL extends \Reports\reportModel
{
    public function getDataTable($headArray)
    {
        $id_month = $headArray['id_Month_RecalculationForREP'];
        $conn = new \backend\Connection();
        $retArr = Array();
        $dataArr = $conn->table('View_GHR_autoCalc_svod')
            ->where('ORG',$this->getORG())
            ->where('id_user',$this->getUser())
            ->where('id_month_create',$id_month)
            ->orderBy("status_street,name_street,house_int,house,id_month")
            ->select()->fetchAll();
        $addres_house = false;
        $table = Array();
        foreach ($dataArr as $key => $rowArray){
            $row_addres_house = "{$rowArray['status_street']} {$rowArray['name_street']}, д.{$rowArray['house']}";
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
        return $retArr;
    }

}