<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\Payments\RegisterOfPayments;


class MODEL extends \Reports\reportModel
{


    public function getDataTable($idUser)
    {
        $conn = new \backend\Connection();
        $data = $conn->table('View_REP_RegisterOfPayments')
            ->where('id_user',$idUser)
            ->orderBy("id")
            //->orderBy("packet,name_street,house_int,room_int")
            ->select();
        $returnArray = Array();

        while ($row = $data->fetch()){
            $returnArray[] = $row;
        }

        return $returnArray;
    }

}