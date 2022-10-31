<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\AreDifferent\decryption_hotWater_byHouse2;


class MODEL extends \Reports\reportModel
{

    public function getDataTable($idUser)
    {
        $conn = new \backend\Connection();
        $conn->table('list_type_accrual_decoding_reports')
            ->where('id_user',$idUser)
            ->delete();
        $conn->table('list_type_accrual_decoding_reports')
            ->set('id_user',$idUser)
            ->set('ORG',$this->getORG())
            ->set('id_type_accrual','7')
            ->insert();

        $data = $conn->table('View_REP_decryption_water2')
            ->where('id_user',$idUser)
            ->where('id_type_accrual',"7")
            ->orderBy('standards_one DESC, tarif3,name_street, int_house ,house')
            ->select();
        $returnArray = Array();

        while ($row = $data->fetch()){
            $returnArray[] = $row;
        }

        return $returnArray;
    }

}