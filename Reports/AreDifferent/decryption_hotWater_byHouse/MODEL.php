<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\AreDifferent\decryption_hotWater_byHouse;


class MODEL extends \Reports\reportModel
{


    public function getDataTable($idUser,$id_type_accrual,$device)
    {
        $conn = new \backend\Connection();
        $data = $conn->table('View_REP_decryption_water_byHouse')
            ->where('id_user',$idUser)
            ->where('id_type_accrual',$id_type_accrual)
         //   ->where('device',$device)
            ->orderBy('name_street,house,id_sub_level_accrual,name_tariff')
            ->select();
        $returnArray = Array();

        while ($row = $data->fetch()){
            $returnArray[] = $row;
        }

        return $returnArray;
    }

}