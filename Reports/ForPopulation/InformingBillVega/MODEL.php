<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\ForPopulation\InformingBillVega;


class MODEL extends \Reports\reportModel
{

    public function getDataArray()
    {
        $conn_head = new \backend\Connection();
        $conn_table = new \backend\Connection();
        $conn_total = new \backend\Connection();
        $ret = Array();

        $id_user = $this->getUser();

        $data_head = $conn_head->table('View_BS_Head')
            ->where('id_user',$id_user)
            ->orderBy('id_LS')
            ->select();

        $data_table = $conn_table->table('View_BS_table_typeAccrual')
            ->where('id_user',$id_user)
            ->orderBy('id_LS,sorting')
            ->select();

        $data_total = $conn_total->table('View_BS_Totals')
            ->where('id_user',$id_user)
            ->orderBy('id_LS,id_level_totals,name_type_accrual')
            ->select();

        while ($value = $data_head->fetch()){
            $ret[$value['id_LS']] = $value;
        }
        while ($value = $data_table->fetch()){
            $val = Array();
            $val['name_type_accrual'] = $value['name_type_accrual'];
            $val['value'] = $value['value1'];
            $val['tarif1'] = $value['tarif1'];
            $val['summa'] = $value['summa'];
            $val['coefficient'] = $value['coefficient'];
            $val['recalc'] = $value['recalc'];
            $val['total'] = $value['summa'] + $value['recalc'];

            if (in_array($value['id_type_calculate'] , Array(16,17,2))){
                $val['value'] = $value['value2'] - $value['value1'];
            }

            if ($value['id_type_accrual'] == 6){
                $val['value'] = $value['value3'] * $value['value4'];

                if ($value['summa2'] <> 0) {
                    $val['summa'] = $value['summa1'];
                    $ret[$value['id_LS']]['table'][] = $val;
                    $val = Array();
                    $val['name_type_accrual'] = $value['name_type_accrual']."(сверх нормы)";
                    $val['value'] = $value['value3'] * $value['value5'];
                    $val['tarif1'] = $value['tarif2'];
                    $val['summa'] = $value['summa2'];
                    $val['coefficient'] = '';
                    $val['recalc'] = '';
                    $val['total'] = '';
                }

                if ($value['summa3'] <> 0){
                    $val['tarif1'] = $value['tarif3'];
                    $val['summa'] = $value['summa3'];
                }
            }

            $ret[$value['id_LS']]['table'][] = $val;
        }
        while ($value = $data_total->fetch()){
            $ret[$value['id_LS']]['total'][] = $value;
        }

        return $ret;
    }
}