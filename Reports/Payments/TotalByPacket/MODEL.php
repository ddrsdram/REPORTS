<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\Payments\TotalByPacket;


class MODEL extends \Reports\reportModel
{


    public function getDataTable($idUser)
    {
        $conn = new \backend\Connection();
        $data = $conn->table('View_REP_RegisterOfPayments')
            ->where('id_user',$idUser)
            ->orderBy("packet")
            ->groupBy("packet")
            ->select("packet, SUM(summa) AS summa");
        $returnArray = Array();

        while ($row = $data->fetch()){
            $returnArray[] = $row;
        }

        return $returnArray;
    }

}