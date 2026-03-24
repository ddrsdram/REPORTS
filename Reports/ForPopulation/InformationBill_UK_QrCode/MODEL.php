<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\ForPopulation\InformationBill_UK_QrCode;


use DB\Connect;
use DB\Connection;
use DB\Table\requisites;
use DB\View\View_BB_Head;

class MODEL extends \Reports\reportModel
{
    public $id_month;
    public function getDataArray()
    {
        $this->id_month = $this->getHeadArray()['id_month'];
        $conn_head = new View_BB_Head();
        $conn_table = new \backend\Connection();
        $conn_total = new \backend\Connection();
        $conn_recalc = new \backend\Connection();
        $ret = Array();

        $d = new requisites();
        $requisites = $d->where($d::ORG,$this->getORG())
            ->where($d::id_month,$this->id_month)
            ->select()
            ->fetch();

        $id_user = $this->getUser();

        $data_head = $conn_head
            ->where(View_BB_Head::id_user,$id_user)
            ->orderBy(View_BB_Head::status_street.","
                .View_BB_Head::iHouse.","
                .View_BB_Head::iRoom.","
                .View_BB_Head::id_LS)
            ->select();

        $id_LS_mainHouse_old = 0;
        $URL = "";
        while ($value = $data_head->fetch()){
            $id_LS_mainHouse = $value['id_LS_mainHouse'];

            if ($id_LS_mainHouse_old != $id_LS_mainHouse){
                $id_LS_mainHouse = $value['id_LS_mainHouse'];
                $URL = $this->getQrCodeData($id_LS_mainHouse);
            }

            $summa = (int)$value['saldoEnd'] * 100;
            $value['QR_SBER'] = Array(
                "QrCodeOffsetX" => 0,
                "QrCodeOffsetY" => 0,
                "sizePixelForQrCode" => 200,
                "data"=>"ST00012|Name={$requisites['name_organization']}|PersonalAcc={$requisites['RSCH']}|BankName={$requisites['name_bank']}|BIC={$requisites['BIK']}|CorrespAcc={$requisites['KSCH']}|Sum={$summa}|persAcc={$value['id_LS']}|PayeeINN={$requisites['INN']}|"
            );
            $value['QR_MainChannel'] = Array(
                "QrCodeOffsetX" => 0,
                "QrCodeOffsetY" => 0,
                "sizePixelForQrCode" => 140,
                "data"=>$requisites['msgMain']
            );
            $value['QR_HomeChannel'] = Array(
                "QrCodeOffsetX" => 0,
                "QrCodeOffsetY" => 0,
                "sizePixelForQrCode" => 140,
                "data"=>$URL
            );
            $ret[$value['id_LS']] = $value;
            $ret[$value['id_LS']]['sod_table'] = Array();
            $ret[$value['id_LS']]['table_recalculate'] = Array();
            $ret[$value['id_LS']]['communal_table'] = Array();
            $ret[$value['id_LS']]['sod'] = '';
            $ret[$value['id_LS']]['communal'] = '';

        }
        $data_table = $conn_table->table('View_BB_table_typeAccrual_viewer')
            ->where('id_user',$id_user)
            //->orderBy('id_LS,id_type_accrual')
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
            $val['edIzm'] = $value['edIzm'];
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

            if (in_array($value['id_type_calculate'] , Array(16,17,2,6))){
                $val['value'] = $value['value2'] - $value['value1'];
            }

            if (in_array($value['id_type_calculate'] , Array(8))){ // расчет ОДН
                $val['value'] = $value['value4'];
            }

            if ($value['id_type_accrual'] == 6){ //если отопление
                $val['value'] = $value['value3'] * $value['value4'];
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
        $LS_OLD1 = 0;
        $col = 0;
        $bStr = false;
        $val = Array('caption1'=>'','name_type_accrual1'=>"","summa1"=>'',
            'caption2'=>'','name_type_accrual2'=>"","summa2"=>'');
        $id_LS = 0;
        while ($value = $data_recalc->fetch()){

            if ($LS_OLD1 <> $value['id_LS']){
                if ($bStr){
                    $ret[$id_LS]['table_recalculate'][] = $val;
                    $bStr = false;
                }
                $val = Array('caption1'=>'','name_type_accrual1'=>"","summa1"=>'',
                    'caption2'=>'','name_type_accrual2'=>"","summa2"=>'');
                $col = 0;
                $LS_OLD1 = $value['id_LS'];
                $bStr = true;
            }
            $col ++;
            if ($col > 2) {
                $col = 1;
            }

            if ($col == 2) {
                $LS_OLD1 = 0;
            }
            $name = 'caption';
            $val[$name.$col] = $value[$name];
            $name = 'name_type_accrual';
            $val[$name.$col] = $value[$name];
            $name = 'summa';
            $val[$name.$col] = $value[$name];
            $id_LS = $value['id_LS'];
        }
        if ($bStr){
            $ret[$id_LS]['table_recalculate'][] = $val;
        }

        return $ret;
    }

    private function getQrCodeData($id_LS_mainHouse)
    {
        $d = new Connect();
        $d->table("LS_head_requisites")
            ->where("ORG",$this->getORG())
            ->where("id_month",$this->id_month)
            ->where("id_LS",$id_LS_mainHouse)
            ->where("id_requisites","9")
            ->select("value_char");
        return  $d->fetchField("value_char");
    }
}