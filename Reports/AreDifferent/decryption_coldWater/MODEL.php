<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\AreDifferent\decryption_coldWater;


class MODEL extends \Reports\reportModel
{
    public function getDataTable($idUser,$id_type_accrual,$device)
    {
        $conn = new \backend\Connection();
        return $conn->table('View_REP_decryption_water')
            ->where('id_user',$idUser)
            ->where('id_type_accrual',$id_type_accrual)
            ->where('device',$device)
            ->orderBy('name_tariff')
            ->select()
            ->fetchAll();

    }

}