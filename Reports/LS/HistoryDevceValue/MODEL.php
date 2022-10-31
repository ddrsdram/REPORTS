<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\LS\HistoryDevceValue;


class MODEL extends \Reports\reportModel
{
    public function getDataTable($idUser)
    {
        $conn = new \backend\Connection();
        $returnArray = Array();

        $data_head = $conn->table('View_REP_historyDeviceValue_head')
            ->where('id_user',$idUser)
            ->orderBy("id_type_accrual,id_device")
            ->select();
        while ($row = $data_head ->fetch()){
            $returnArray[$row['id_device']] = $row;
        }

        //
        $data = $conn->table('View_REP_historyDeviceValue')
            ->where('id_user',$idUser)
            ->orderBy("id_type_accrual,id_device,id_device_value DESC")
            ->select();

        while ($row = $data->fetch()){
            $returnArray[$row['id_device']]['table'][] = $row;
        }


        return $returnArray;
    }
}