<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\AreDifferent\accrualsFor_typeAccrual_ByHouse_AllPeriod;


class MODEL extends \Reports\reportModel
{
    public function getDataTable()
    {
        $conn = new \backend\Connection();
        $id_type_accrual = $conn->table('type_accrual_maintenance')
            ->where('ORG',$this->getORG())
            ->select('id_type_accrual')->fetchField('id_type_accrual');

        $dataArray = $this->getHeadArray();
        $id_month = $dataArray['id_month0'];
        return $conn->table('proc_REP_accrualsFor_typeAccrual_ByHouse')
            ->set('id_user',$this->getUser())
            ->set('ORG',$this->getORG())
            ->set('id_month',$id_month)
            ->set('id_type_accrual',$id_type_accrual)
            ->set('AllPeriod',"0")

            ->SQLExec()
            ->fetchAll();
    }

}