<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\Tribunal\AddressListDebitSixMonth;


class MODEL extends \Reports\reportModel
{


    public function getDataTable($manageTable)
    {

        $conn = new \backend\Connection();

        $id_month = $conn->table($manageTable)
            ->where('id_user',$this->getUser())
            ->select()->fetchField('id_month');

        $data = $conn->table('View_tribunal_full')
            ->where('id_month',$id_month)
            ->where('ORG',$this->getORG())
            ->where('f_filter','1')
            ->orderBy('name_street,house_int,room_int')
            ->select();
        $returnArray = $data->fetchAll();
        return $returnArray;
    }

}