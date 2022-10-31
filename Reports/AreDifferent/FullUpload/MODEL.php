<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\AreDifferent\FullUpload;


class MODEL extends \Reports\reportModel
{

    public function getDataTable($idUser)
    {
        $conn = new \backend\Connection();
        return $conn->table('View_FullUpload')
            ->where('id_user',$idUser)
            ->orderBy('id_LS, sorting,id_type_accrual')
            ->select();
    }

}