<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\AreDifferent\Report1;


class MODEL extends \Reports\reportModel
{

    public function getDataTable($idUser)
    {
        $conn = new \backend\Connection();
        $returnArray = $conn->table('View_REP_AreDifferent')
            ->where('id_user',$idUser)
            ->orderBy('sorting,id_type_accrual,id_level_totals,id_sub_level_accrual')
            ->select()->fetchAll();


        return $returnArray;
    }

}