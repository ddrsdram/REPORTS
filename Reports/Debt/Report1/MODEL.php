<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\Debt\Report1;


class MODEL extends \Reports\reportModel
{

    public function getDataTable($idUser)
    {
        $conn = new \backend\Connection();
        $data = $conn->table('View_REP_D_Debt')
            ->where('id_user',$idUser)
            ->where('saldo_end',$this->getBodyTextOnly()," > ")
            ->orderBy('name_JEU,name_street,sort_house,sort_room')
            ->select();
        $returnArray = Array();

        while ($row = $data->fetch()){
            $returnArray[] = $row;
        }

        return $returnArray;
    }

}