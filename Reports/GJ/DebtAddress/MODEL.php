<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\GJ\DebtAddress;


class MODEL extends \Reports\reportModel
{

    public function getDataArray()
    {
        $d = new \DB\View\View_GJ_printDebtAddress();
        $HA = $this->getHeadArray();
        $id_month = $HA['id_month'];
        return $d
            ->where($d::guid_id,$this->getORG())
            ->where($d::id_month,$id_month)
            ->select()
            ->fetchAll();
    }

}