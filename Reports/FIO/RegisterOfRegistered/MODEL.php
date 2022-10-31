<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\FIO\RegisterOfRegistered;


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

    public function getDataArray()
    {
        $retArray = Array();
        $conn = new \backend\Connection();
        $dataArray1 =  $conn->table('View_FIO')
            ->where("ORG",5)
            ->where("id_month",200)
            ->where("id_LS_propiska","0","<>")
            ->where("f_person_tmp","0")
            ->orderBy('name_street,house,room')
            ->select()
            ->fetchAll();

        $dataArray2 =  $conn->table('View_FIO')
            ->where("ORG",5)
            ->where("id_month",200)
            ->where("id_LS_propiska","0","<>")
            ->where("f_person_tmp","1")
            ->orderBy('name_street,house,room')
            ->select()
            ->fetchAll();
        foreach ($dataArray1 as $key => $item){
            $retArray[$item['id_LS_propiska']][$item['id']] = $item;
        }

        foreach ($dataArray2 as $key => $item){
            $retArray[$item['id_LS_propiskaTmp']][$item['id']] = $item;
        }

        return $retArray;
    }

}