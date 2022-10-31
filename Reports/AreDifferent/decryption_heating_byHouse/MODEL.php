<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\AreDifferent\decryption_heating_byHouse;


class MODEL extends \Reports\reportModel
{

    public function getDataTable($idUser)
    {
        $conn = new \backend\Connection();
        $data = $conn->table('View_REP_decryption_heating_byHouse')
            ->where('id_user',$idUser)
            ->orderBy('name_street,house')
            ->select();
        $returnArray = Array();

        while ($row = $data->fetch()){
            $returnArray[] = $row;
        }

        return $returnArray;
    }

}