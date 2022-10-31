<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\AreDifferent\decryption_canalisation_byHouse2;


class MODEL extends \Reports\reportModel
{

    public function getDataTable($idUser)
    {
        $conn = new \backend\Connection();

        $conn->table('list_type_accrual_decoding_reports')
            ->where('id_user',$idUser)
            ->delete();
        $conn->table('list_type_accrual_decoding_reports')
            ->set('id_user',$idUser)
            ->set('ORG',$this->getORG())
            ->set('id_type_accrual','9')
            ->insert();

        return $conn->table('View_REP_decryption_canalisation2_end')
            ->where('id_user',$idUser)
            ->orderBy('standard, name_street, house')
            ->select()
            ->fetchAll();

    }

}