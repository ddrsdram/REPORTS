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

        return $conn->table('View_GHR_autoCalc_svod')
            ->where('ORG',$this->getORG())
            ->where('id_user',$this->getUser())
            ->where('id_month_create',$id_month)
            ->orderBy("name_street,house_int,id_month")
            ->select()->fetchAll();

    }

}