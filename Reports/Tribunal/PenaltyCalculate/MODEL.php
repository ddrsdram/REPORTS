<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\Tribunal\PenaltyCalculate;


use DB\Table\recalculation_global_type_accrual;

class MODEL extends \Reports\reportModel
{
    private $dateStart;



    public function getHeadArray()
    {
        $data = parent::getHeadArray();
        $data['dateStart'] = $this->dateStart;

        $d = new \DB\Table\tribunal_settings();
        $arr = $d->where($d::ORG,$this->getORG())
            ->select()->fetch();
        foreach ($arr as $key => $value){
            $data[$key] = $value;
        }
        return $data;
    }


    public function getDataTable()
    {

        $conn = new \backend\Connection();
        $HeadArray = $this->getHeadArray();
        $id_LS = $HeadArray['id_LS'];
        $data = $conn->table('proc_Penalty_get_calculateForOneLS')
            ->set('id_user',$this->getUser())
            ->set('ORG',$this->getORG())
            ->set('id_LS',$id_LS)
            ->SQLExec();


        $Arr = $data->fetchAll();
        $returnArray = Array();
        $dataArr  = Array();
        $idOperation = -1;
        $summaPenalty = 0;
        foreach ($Arr as $item){
            if ($idOperation == -1 ) {// если у нас начала цикла то запоминаем первый элемент операции
                $idOperation = $item['idOperation'];
                $dateStart = $item['id_date'];
                $this->dateStart = date('d.m.Y',strtotime($dateStart));
            }

            if ($idOperation != $item['idOperation']){
                $idOperation = $item['idOperation'];
                $returnArray[] = Array("data"=>$dataArr,"dateStart"=>$dateStart,"SumPenalty"=>$summaPenalty);
                $dateStart = $item['id_date'];
                $summaPenalty = 0;
                $dataArr  = Array();
            }
            $dataArr[] = $item;
            $summaPenalty = $summaPenalty + $item['SumPenalty'];
        }
        return $returnArray;
    }

    public function getDateStart()
    {
        return $this->dateStart;
    }
}