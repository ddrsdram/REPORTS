<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\FIO\CertificateToCourt;


class MODEL extends \Reports\reportModel
{
    private $f_registration = "0";

    public function getDataArrayHead()
    {
        $conn = new \backend\Connection();


        $returnArray = $this->getHeadArray();
        $data = $conn->table('View_REP_FIO_Certificate_1')
            ->where('id_user',$this->getUser())
            ->select();

        $transName = new \models\NameCaseLib\NCLNameCaseRu();

        $res = $data->fetch();
        foreach ($res as $key => $value){
            $returnArray[$key] = $value;
        }

        $FIO =  $transName->q(
            $this->mb_strtoupper_first($returnArray['fam'])." ".
            $this->mb_strtoupper_first($returnArray['im'])." ".
            $this->mb_strtoupper_first($returnArray['ot']), \models\NameCaseLib\NCL\NCL::$DATELN);
        $returnArray['FIO'] = $FIO;
        $var = 'birthday';
        $start_date=new \DateTime($returnArray[$var]);
        $returnArray[$var] = $start_date->format('d.m.Y');
        $var = 'data_create';
        $start_date=new \DateTime($returnArray[$var]);
        $returnArray[$var] = $start_date->format('d.m.Y');

        $var = 'f_registration';
        $returnArray[$var] = $this->f_registration;

        return $returnArray;
    }

    public function getDataArray()
    {
        $conn = new \backend\Connection();

        $headArray = $this->getHeadArray();


        $conn->table('View_REP_FIO_Certificate_All')
            ->where('id_user',$this->getUser())
            ->where('ORG',$this->getORG());
            //->where('id_doc',-1)
        if (array_key_exists('ChildrenOff',$headArray)){
            if ($headArray['ChildrenOff'] == '0')
                $conn->where('years',18,'>=');
        }

        $data = $conn->select();

        $transName = new \models\NameCaseLib\NCLNameCaseRu();

        $returnArray = Array();
        $this->f_registration = "0";
        while ($row = $data->fetch()){
            $this->f_registration = "1";
            $FIO =  $this->mb_strtoupper_first($row['fam'])." ".$this->mb_strtoupper_first($row['im'])." ".$this->mb_strtoupper_first($row['ot']);
            $row['FIO'] = $FIO;

            $var = 'birthday';
            $start_date=new \DateTime($row[$var]);
            $row[$var] = $start_date->format('d.m.Y');

            $var = 'data_create';
            $start_date=new \DateTime($row[$var]);
            $row[$var] = $start_date->format('d.m.Y');

            $returnArray[] = $row;
        }

        return $returnArray;
    }

    private function mb_strtoupper_first($str, $encoding = 'UTF8')
    {
        $str = mb_strtolower($str, $encoding);
        return
            mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding) .
            mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
    }
}