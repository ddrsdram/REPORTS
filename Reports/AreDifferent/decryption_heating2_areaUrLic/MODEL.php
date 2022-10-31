<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\AreDifferent\decryption_heating2_areaUrLic;


class MODEL extends \Reports\reportModel
{

    public function getDataTable($idUser)
    {
        $conn = new \backend\Connection();
        return$conn->table('View_REP_decryption_heating2_byHouse_ValueAndArea1')
            ->where('id_user',$idUser)
            ->orderBy('name_street,house_int,house')
            ->select()
            ->fetchAll();
    }

}