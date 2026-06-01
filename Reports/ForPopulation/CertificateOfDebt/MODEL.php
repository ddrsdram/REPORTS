<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\ForPopulation\CertificateOfDebt;


class MODEL extends \Reports\reportModel
{
    public $debtSum = 0;

    public function getHeadArray()
    {
        $headArray = parent::getHeadArray();
        $conn_head = new \backend\Connection();

        $data_head = $conn_head->table('View_BS_Head')
            ->where('id_user',$this->getUser())
            ->orderBy('id_LS')
            ->select();
        while ($res = $data_head->fetch()){
            foreach ($res as $key => $value){
                $headArray[$key] = $value;
            }
        }

        $headArray['debtSum'] = $this->debtSum;
        //
        $month = [
            1 => 'января',
            2 => 'февраля',
            3 => 'марта',
            4 => 'апреля',
            5 => 'мая',
            6 => 'июня',
            7 => 'июля',
            8 => 'августа',
            9 => 'сентября',
            10 => 'октября',
            11 => 'ноября',
            12 => 'декабря'
        ];

        $dataText = date('d') . ' ' . $month[date('n')] . ' ' . date('Y');

        if ($this->debtSum <= 0){
            $headArray['debtSum_txt'] = "На $dataText года задолженности нет";
            $headArray['debtSum_txt1'] = '';
        }else{

            $sum = substr($this->debtSum,0,strlen($this->debtSum)-2);

            $headArray['debtSum_txt'] = "Задолженность на $dataText года составляет $sum рублей";
            $headArray['debtSum_txt1'] = \models\Num2Str::getText($this->debtSum);
        }

        $bookkeeperArr = explode(' ',$headArray['bookkeeper']);
        $headArray['bookkeeper'] = $bookkeeperArr[0] . ' ' . mb_substr($bookkeeperArr[1],0,1) . '. ' . mb_substr($bookkeeperArr[2],0,1). '.';

        return $headArray;
    }


    public function getDataTable()
    {
        $conn_table = new \backend\Connection();
        $ret = Array();
        $data_table = $conn_table->table('View_BS_table_typeAccrual_viewer')
            ->where('id_user',$this->getUser())
            ->orderBy('id_LS,sorting')
            ->select();

        while ($value = $data_table->fetch()){
            $val = Array();
            $val['name_type_accrual'] = $value['name_type_accrual'];
            $val['value'] = $value['value1'];
            $val['tarif1'] = $value['tarif1'];
            $val['summa'] = $value['summa'];
            $val['coefficient'] = $value['coefficient'];
            $val['recalc'] = $value['recalculation'];
            $val['total'] = $value['summa'] + $value['recalculation'];
            $val['value_standard'] = $value['value_standard'];
            //
            if (in_array($value['id_type_calculate'] , Array(16,17,2))){
                $val['value'] = $value['value2'] - $value['value1'];
            }

            if (in_array($value['id_type_calculate'] , Array(8))){ // расчет ОДН
                $val['value'] = $value['value1'] * $value['value3'];
            }

            if ($value['id_type_accrual'] == 6){
                $val['value'] = $value['value3'] * $value['value4'];

                if ($value['summa2'] <> 0) {
                    $val['summa'] = $value['summa1'];

                    $ret[] = $val;

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
            $ret[] = $val;
        }

        return $ret;
    }

    public function getDataTotal()
    {
        $ret = Array();
        $conn_total = new \backend\Connection();
        $data_total = $conn_total->table('View_BS_Totals')
            ->where('id_user',$this->getUser())
            ->orderBy('id_LS,id_level_totals,name_type_accrual')
            ->select();

        $today = (int) date("d"); // Текущая день
        if ($today <= 15)
            $total = 10;
        else
            $total = 60;

        while ($value = $data_total->fetch()){

            if ($value['id_level_totals'] == $total){
                $this->debtSum = $value['summa'];
            }

            $ret[] = $value;
        }


        return $ret;
    }
}