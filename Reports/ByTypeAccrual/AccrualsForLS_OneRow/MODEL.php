<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\ByTypeAccrual\AccrualsForLS_OneRow;


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
            ->orderBy('name_JEU,name_street,int_house,int_room')
            ->select();

        $data_table = $conn_table->table('View_AFY_accruals')
            ->where('id_user',$id_user)
            ->orderBy('id_LS,sorting')
            ->select();


        while ($value = $data_head->fetch()){
            $ret[$value['id_LS']] = $value;
        }
        while ($value = $data_table->fetch()){
            $val = Array();
            $ret[$value['id_LS']]['table'][] = $value;
        }
        return $ret;
    }

    public function getTypeAccrual()
    {
        $conn = new \backend\Connection();
        return  $conn->table('View_AFY_usedTypeAccrual')
            ->where('ORG',$this->getORG())
            ->where('id_user',$this->getUser())
            ->where('id','0','<>')
            ->orderBy('sorting')
            ->select("name,detailing_general_report");
    }
}