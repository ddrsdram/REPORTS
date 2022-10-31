<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\Tribunal\PenaltyCalculate;


class MODEL extends \Reports\reportModel
{
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

}