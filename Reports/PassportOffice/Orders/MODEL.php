<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\PassportOffice\Orders;


class MODEL extends \Reports\reportModel
{

    public function getDataTable($idUser)
    {
        $conn = new \backend\Connection();
        $returnArray = $conn->table('View_REP_Orders')
            ->where('id_user',$idUser)
            ->orderBy('name_street,int_house,int_room,id_LS')
            ->select()->fetchAll();

//View_REP_PassportOffice_registration
        return $returnArray;
    }

}