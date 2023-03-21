<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\PassportOffice\Registration;


class MODEL extends \Reports\reportModel
{

    public function getDataTable($idUser)
    {
        $conn = new \backend\Connection();
        $returnArray = $conn->table('View_REP_PassportOffice_registration')
            ->where('id_user',$idUser)
            ->orderBy('status_street,name_street,int_house,int_room,id_LS,fam,im,ot')
            ->select()->fetchAll();

//
        return $returnArray;
    }

}