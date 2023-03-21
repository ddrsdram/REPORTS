<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\PassportOffice\ListOfDischarged;


class MODEL extends \Reports\reportModel
{

    public function getDataTable($idUser)
    {
        $conn = new \backend\Connection();
        $returnArray = $conn->table('View_REP_Orders')
            ->where('id_user',$idUser)
            ->select()->fetchAll();


        return $returnArray;
    }

}