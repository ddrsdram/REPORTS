<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\AreDifferent\decryption_canalisation;


class MODEL extends \Reports\reportModel
{
    public function getDataTable($idUser)
    {
        $conn = new \backend\Connection();
        return $conn->table('View_REP_decryption_canalisation_end')
            ->where('id_user',$idUser)
            ->select()
            ->fetchAll();
    }

}