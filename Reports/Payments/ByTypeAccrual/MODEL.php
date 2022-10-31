<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\Payments\ByTypeAccrual;


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

    public function getDataTable()
    {
        $returnArray = Array();
        $returnArray['TA'] = $this->getTypeAccrual(); // Array('Columns'=>$col,'Data'=>$ret_array);
        $returnArray['Data'] = $this->getFullData();

        return $returnArray;
    }

    private function getFullData()
    {

        $returnArray = $this->getHead();
        $Accrual = $this->getAccrual();
        while ($res = $Accrual->fetch()){
            $returnArray[$res['id_LS']]['Accruals'][$res['id_type_accrual']] = $res;
        }

        return $returnArray;

    }
    private function getHead()
    {
        $conn = new \backend\Connection();
        $data =  $conn->table('View_REP_paymentByTypeAccrual_DH_0')
            ->where('ORG',$this->getORG())
            ->where('id_user',$this->getUser())
            ->orderBy('id_JEU,name_street,int_house,int_room,id_LS')
            ->select();

        $returnArray = Array();
        while ($res = $data->fetch()){
            $returnArray[$res['id_LS']]['Head'] = $res;
        }


        return $returnArray;
    }
    private function getAccrual()
    {
        $conn = new \backend\Connection();
        return $conn->table('View_REP_paymentByTypeAccrual_DA_0')
            ->where('ORG',$this->getORG())
            ->where('id_user',$this->getUser())
            ->orderBy('sorting')
            ->select();
    }

    private function getTypeAccrual()
    {
        $ret_array = Array();
        $conn = new \backend\Connection();
        $col = 1;
        $res= $conn->table('View_REP_paymentByTypeAccrual_TA_1')
            ->where('ORG',$this->getORG())
            ->where('id_user',$this->getUser())
            ->orderBy('sorting')
            ->select()->fetchAll();


        foreach ($res as $key => $value){
            $ret_array[$value['id_type_accrual']] = $value;
            $ret_array[$value['id_type_accrual']]['col'] = $col;
            $col++;
        }

        return Array('Columns'=>$col-1,'Data'=>$ret_array);

    }
}