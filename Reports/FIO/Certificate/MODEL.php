<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\FIO\Certificate;


class MODEL extends \Reports\reportModel
{

    public function getDataArrayHead()
    {
        $conn = new \backend\Connection();


        $data = $conn->table('View_REP_FIO_Certificate_1')
            ->where('id_user',$this->getUser())
            ->select();

        $transName = new \models\NameCaseLib\NCLNameCaseRu();
        $returnArray = Array();
        $returnArray = $data->fetch();

        $FIO =  $transName->q(
            $this->mb_strToUpper_first($returnArray['fam'])." ".
            $this->mb_strToUpper_first($returnArray['im'])." ".
            $this->mb_strToUpper_first($returnArray['ot']), \models\NameCaseLib\NCL\NCL::$DATELN);
        $returnArray['FIO'] = $FIO;
        $start_date=new \DateTime($returnArray['birthday']);
        $returnArray['birthday'] = $start_date->format('d.m.Y');


        return $returnArray;
    }

    public function getDataArray()
    {
        $conn = new \backend\Connection();


        $data = $conn->table('View_REP_FIO_Certificate_All')
            ->where('id_user',$this->getUser())
            ->where('ORG',$this->getORG())
            ->select();

        $transName = new \models\NameCaseLib\NCLNameCaseRu();

        $returnArray = Array();
        while ($row = $data->fetch()){

            $FIO =  $this->mb_strToUpper_first($row['fam'])." ".$this->mb_strToUpper_first($row['im'])." ".$this->mb_strToUpper_first($row['ot']);
            $row['FIO'] = $FIO;
            $start_date=new \DateTime($row['birthday']);
            $row['birthday'] = $start_date->format('d.m.Y');
            $returnArray[] = $row;
        }

        return $returnArray;
    }

    private function mb_strToUpper_first($str, $encoding = 'UTF8')
    {
        $str = mb_strtolower($str, $encoding);
        return
            mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding) .
            mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
    }
}