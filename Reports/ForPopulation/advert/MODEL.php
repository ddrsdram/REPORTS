<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\ForPopulation\advert;


class MODEL extends \Reports\reportModel
{


    public function getDataTable()
    {
        $conn_table = new \backend\Connection();
        $ret = Array();
        $data_table = $conn_table->table('View_REP_ABFD_accrual_By_FrontDoor')
            ->where('id_user',$this->getUser())
            ->orderBy('name_street,i_house,house, frontDoor,i_room, id_LS')
            ->select();

        $id_arr_old='0';
        $sub_table = Array();

        while ($value = $data_table->fetch()){

            $id_arr = "{$value['id_street']}_{$value['house']}_{$value['frontDoor']}";

            if ($id_arr_old != $id_arr){
                if($id_arr_old != "0"){
                    // запись субтаблицы в основной массив
                    $ret[$id_arr_old]['table2'] = $sub_table;
                }
                $id_arr_old = $id_arr;
                $sub_table = Array();
            }
            $sub_table[] = $value;
        }
        $ret[$id_arr_old]['table2'] = $sub_table;

        return $ret;
    }


}