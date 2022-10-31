<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\ForPopulation\InformationBillBigRSO;


class MODEL extends \Reports\reportModel
{

    public function getDataArray()
    {
        $conn_head = new \backend\Connection();
        $conn_table = new \backend\Connection();
        $conn_total = new \backend\Connection();
        $conn_recalc = new \backend\Connection();
        $ret = Array();

        $id_user = $this->getUser();

        $data_head = $conn_head->table('View_BB_Head')
            ->where('id_user',$id_user)
            ->orderBy('name_street,iHouse,iRoom')
            ->select();
        while ($value = $data_head->fetch()){
            $ret[$value['id_LS']] = $value;
            $ret[$value['id_LS']]['table_recalculate'] = Array();
            $ret[$value['id_LS']]['communal_table']  = Array();
            $ret[$value['id_LS']]['standards_volume']  = Array();

        }
        $data_table = $conn_table->table('View_BB_table_typeAccrual')
            ->where('id_user',$id_user)
            ->orderBy('id_LS,id_typeOfService,sorting')
            ->select();

        $LS_OLD = 0;
        $sod = 'No';
        $communal = 'No';
        while ($value = $data_table->fetch()){
            if ($LS_OLD <> $value['id_LS']){
                $sod = 'No';
                $communal = 'No';
                $LS_OLD = $value['id_LS'];
            }

            $val = Array();
            $val['name_type_accrual'] = $value['name_type_accrual'];
            $val['name_company'] = $value['name_company'];
            $val['tarif1'] = $value['tarif1'];
            $val['tarif2'] = $value['tarif2'];
            $val['tarif3'] = $value['tarif3'];

            $val['acruals30'] = $value['acruals30'];
            $val['summa1'] = $value['summa1'];
            $val['summa2'] = $value['summa2'];
            $val['summa3'] = $value['summa3'];
            $val['acruals32'] = $value['acruals32'];
            $val['delta_summa'] = $value['delta_summa'];
            $val['delta_tarif'] = $value['delta_tarif'];
            $val['recalculation'] = $value['recalculation'];
            $val['total'] = $value['acruals30'] + $value['recalculation'];

            $val['value'] = $value['value1'];
            $id_type_calculate = $value['id_type_calculate'];
            if (in_array($id_type_calculate, Array(2,16,17,24))){
                $val['value'] = $value['value2'] - $value['value1'];
            }
            if (in_array($id_type_calculate, Array(23))){
                $val['value'] = $value['value4'];
            }


            if ($value['id_type_accrual'] == 6){ //если отопление
                $val['value'] = $value['value3'] * ($value['value4'] + $value['value5'] + $value['value6']);
            }


            if ($value['id_typeOfService'] == 1){
                $sod = 'Yes';
                $ret[$value['id_LS']]['sod_table'][] = $val;
            }
            if ($value['id_typeOfService'] == 2){
                $communal = 'Yes';
                $ret[$value['id_LS']]['communal_table'][] = $val;
            }
            $ret[$value['id_LS']]['sod'] = $sod;
            $ret[$value['id_LS']]['communal'] = $communal;
        }


        $data_total = $conn_total->table('View_BB_standards_volume')
            ->where('id_user',$id_user)
            ->orderBy('id_LS,id_type_accrual')
            ->select();
        while ($value = $data_total->fetch()){
            $ret[$value['id_LS']]['standards_volume'][] = $value;
        }


        $data_recalc = $conn_recalc->table('View_BB_table_recalculate')
            ->where('id_user',$id_user)
            ->orderBy('id_LS,id_type_accrual')
            ->select();
        $col = 0;
        $bStr = false;
        $val = Array('caption1'=>'','name_type_accrual1'=>"","summa1"=>'',
                    'caption2'=>'','name_type_accrual2'=>"","summa2"=>'');
        $id_LS = 0;
        $LS_OLD1 = 0;
        $indexVal = 0;
        while ($value = $data_recalc->fetch()){
            $id_LS = $value['id_LS'];

            if ($LS_OLD1 <> $id_LS){
                /*
                if ($bStr){
                    $ret[$id_LS]['table_recalculate'][] = $val;
                    $bStr = false;
                }
                */

                if ($LS_OLD1 <> 0){
                    $ret[$id_LS]['table_recalculate'] = $val;
                }
                $val = Array();
                $indexVal = 0;

                $val[$indexVal] = Array('caption1'=>'','name_type_accrual1'=>"","summa1"=>'',
                             'caption2'=>'','name_type_accrual2'=>"","summa2"=>'');
                $col = 0;
                $LS_OLD1 = $id_LS;
                $bStr = true;
            }
            $col ++;
            if ($col > 2) {
                $indexVal ++ ;

                $val[$indexVal] = Array('caption1'=>'','name_type_accrual1'=>"","summa1"=>'',
                    'caption2'=>'','name_type_accrual2'=>"","summa2"=>'');

                $col = 1;
            }
            
            if ($col == 2) {
                $LS_OLD1 = 0;
            }


            $name = 'caption';
            $val[$indexVal][$name.$col] = $value[$name];
            $name = 'name_type_accrual';
            $val[$indexVal][$name.$col] = $value[$name];
            $name = 'summa';
            $val[$indexVal][$name.$col] = $value[$name];

        }

        $ret[$id_LS]['table_recalculate'] = $val;

        if (array_key_exists(0,$ret))
            unset($ret[0]);

        return $ret;
    }

}