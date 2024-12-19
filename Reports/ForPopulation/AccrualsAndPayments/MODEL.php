<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\ForPopulation\AccrualsAndPayments;


class MODEL extends \Reports\reportModel
{
    public $ret;
    public function getDataArray()
    {
        $conn_head = new \backend\Connection();
        $ret = Array();

        $id_user = $this->getUser();

        $data_head = $conn_head->table('View_AFY_head')
            ->where('id_user',$id_user)
            ->orderBy('id_month DESC')
            ->select();


        while ($value = $data_head->fetch()){
            $this->ret[$value['id_month']] = $value;
        }
        return $this->ret;
    }

    public function getHeadArray()
    {
        $ret = Array();
        if (strlen($this->dataReports_Array['headArray'])>1) {
            $ret = json_decode($this->dataReports_Array['headArray'], true);
            $row = current($this->ret);
            foreach ($row as $key => $value){
                $ret[$key] = $value;
            }
            $LS = $ret['id_LS'];
            $street = $ret['name_street'];
            $house = $ret['house'];
            $room = $ret['room'];

            $ret['fullAddress'] = "Лицевой счет:$LS Адрес: Улица $street, дом $house, квартира $room";

        }
        return $ret;
    }
}