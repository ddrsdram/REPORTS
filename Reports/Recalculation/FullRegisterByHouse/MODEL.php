<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\Recalculation\FullRegisterByHouse;


class MODEL extends \Reports\reportModel
{
    public function getDataTable($headArray)
    {
        $id_month = $headArray['id_Month_RecalculationForREP'];
        $conn = new \backend\Connection();

        $data = $conn->table('View_REP_recalculation_RegisterByHouse')
            ->where('ORG',$this->getORG())
            ->where('id_month',$id_month)
//            ->where('id',$id)
            ->orderBy("id_type_accrual,id_JEU,name_street,house")
            ->select();
        $returnArray = Array();

        while ($row = $data->fetch()){
            $returnArray[] = $row;
        }

        return $returnArray;
    }

}