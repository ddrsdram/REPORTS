<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\Payments\TotalByOrganization;


class MODEL extends \Reports\reportModel
{


    public function getDataTable($idUser)
    {
        $conn = new \backend\Connection();
        $data = $conn->table('View_REP_RegisterOfPayments')
            ->where('id_user',$idUser)
            ->orderBy("name_organization")
            ->groupBy("name_organization")
            ->select("name_organization, SUM(summa) AS summa");
        $returnArray = Array();

        while ($row = $data->fetch()){
            $returnArray[] = $row;
        }

        return $returnArray;
    }

}