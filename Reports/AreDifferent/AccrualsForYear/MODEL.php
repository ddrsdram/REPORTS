<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\AreDifferent\AccrualsForYear;


class MODEL extends \Reports\reportModel
{

    public function getDataArray()
    {
        $conn_head = new \backend\Connection();
        $conn_table = new \backend\Connection();
        $ret = Array();

        $id_user = $this->getUser();

        $data_head = $conn_head->table('View_AFY_head')
            ->where('id_user',$id_user)
            ->orderBy('id_month DESC')
            ->select();

        $data_table = $conn_table->table('View_AFY_accruals')
            ->where('id_user',$id_user)
            ->orderBy('id_month DESC,sorting')
            ->select();

        while ($value = $data_head->fetch()){
            $ret[$value['id_month']] = $value;

        }
        while ($value = $data_table->fetch()){
            $val = Array();
            $ret[$value['id_month']]['table'][] = $value;
        }
        return $ret;
    }

    public function getTypeAccrual()
    {
        $conn = new \backend\Connection();
        return  $conn->table('View_AFY_usedTypeAccrual')
            ->where('id_user',$this->getUser())
            ->where('ORG',$this->getORG())
            ->orderBy('sorting')
            ->select("name,detailing_general_report");
    }
}