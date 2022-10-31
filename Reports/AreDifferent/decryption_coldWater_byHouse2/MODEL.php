<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\AreDifferent\decryption_coldWater_byHouse2;


class MODEL extends \Reports\reportModel
{


    public function getDataTable($idUser,$type_accrual,$sortByAddress = false,$filterSum = false)
    {
        $conn = new \backend\Connection();

        $conn->table('list_type_accrual_decoding_reports')
            ->where('id_user',$idUser)
            ->delete();
        $conn->table('list_type_accrual_decoding_reports')
            ->set('id_user',$idUser)
            ->set('ORG',$this->getORG())
            ->set('id_type_accrual',$type_accrual)
            ->insert();

        $conn->table('View_REP_decryption_water2')
            ->where('id_user',$idUser)
            ->where('id_type_accrual',$type_accrual);

        if ($filterSum )
            $conn->where('summControll','0','<>');

        if ($sortByAddress)
            $conn->orderBy('name_street, house');
        else
            $conn->orderBy('standards_one DESC, name_street, house');

        $data = $conn->select();
        $returnArray = false;

        while ($row = $data->fetch()){
            $returnArray[] = $row;
        }

        return $returnArray;
    }

}