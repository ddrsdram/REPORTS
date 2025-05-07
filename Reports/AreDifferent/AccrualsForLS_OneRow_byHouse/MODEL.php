<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\AreDifferent\AccrualsForLS_OneRow_byHouse;


class MODEL extends \Reports\reportModel
{

    public function getDataArray()
    {
        $conn_head = new \backend\Connection();
        $conn_table = new \backend\Connection();
        $ret = Array();

        $id_user = $this->getUser();

        $data_head = $conn_head->table('View_AFY_head_byHouse')
            ->where('id_user',$id_user)
            ->orderBy('name_street,int_house,house')
            ->select();

        $data_table = $conn_table->table('View_AFY_accruals_byHouse')
            ->where('id_user',$id_user)
            ->where('id_street','0',' <> ')
            ->orderBy('id_street,int_house,house,sorting')
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
        $conn = new \backend\Connection();
        return  $conn->table('View_AFY_usedTypeAccrual')
            ->where('ORG',$this->getORG())
            ->where('id_user',$this->getUser())
            ->where('id','0','<>')
            ->orderBy('sorting')
            ->select("name,detailing_general_report");
    }
}