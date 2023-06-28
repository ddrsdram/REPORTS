<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\AreDifferent\Report2;


class MODEL extends \Reports\reportModel
{
    private $sumColumn;
    private $JEU_Array;
    private $typeAccrual_Array;

    function __construct($id_report)
    {
        parent::__construct($id_report);
        $this->sumColumn = 0;
    }

    public function getDataTable($idUser,$ORG)
    {
        $returnArray = Array();
        $returnArray['typeAccrual'] = $this->getTypeAccrual($idUser,$ORG);
        $returnArray['totals'] = $this->getTotals($idUser,$ORG);

        $returnArray['columns'] = $this->sumColumn;
        return $returnArray;
    }

    public function getTypeAccrual($idUser,$ORG)
    {
        $conn = new \backend\Connection();
        $this->typeAccrual_Array = Array();
        $data = $conn->table('View_REP_ADT_used_type_accrual')
            ->where('id','0','<>')
            ->where("ORG",$ORG)
            ->where("id_user",$idUser)
            ->orderBy('sorting')
            ->select();
        while ($res = $data->fetch()){
            $this->typeAccrual_Array[$res['id']] = $res;
            $this->sumColumn += $res['detailing_general_report'];
        }

        return $this->typeAccrual_Array;
    }


    private function getTotals($idUser,$ORG)
    {

        $conn = new \backend\Connection();
        $data = $conn->table('View_REP_ADT_AreDifferent_table1')
            //->where("ORG",$ORG)
            ->where("id_user",$idUser)
            ->orderBy('region,id_JEU')
            ->select();

        while ($res = $data->fetch()){
                $this->JEU_Array[$res['region']."_".$res['id_JEU']] = $res;
        }

        $data = $conn->table('View_REP_ADT_AreDifferent_typeAccruals')
            ->where("ORG",$ORG)
            ->where("id_user",$idUser)
            ->orderBy('region,sorting')
            ->select();
        while ($res = $data->fetch()){
                $this->JEU_Array[$res['region']."_".$res['id_JEU']]['data_type_accrual'][$res['id_type_accrual']] = $res;
        }
        return $this->JEU_Array;
    }
}