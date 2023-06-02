<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\FIO\CertificateOfTheDeceased;


class MODEL extends \Reports\reportModel
{

    private $data = Array();

    public function getDataArrayHead()
    {
        $conn = new \backend\Connection();


        $returnArray = $this->getHeadArray();
        $data = $conn->table('View_REP_FIO_Certificate_OfTheDeceased_JR')
            ->where('id_user',$this->getUser())
            ->select();


        $res = $data->fetch();
        foreach ($res as $key => $value){
            $returnArray[$key] = $value;
        }


        $FIO =
            $this->mb_strtoupper_first($returnArray['fam'])." ".
            $this->mb_strtoupper_first($returnArray['im'])." ".
            $this->mb_strtoupper_first($returnArray['ot']);
        $transName = new \models\NameCaseLib\NCLNameCaseRu();
        $gender = $transName->genderDetect($FIO);
        if ($gender == \models\NameCaseLib\NCL\NCL::$MAN){
            $returnArray['MW'] = 'умерший';
        }else{
            $returnArray['MW'] = 'умершая';
        }


        $returnArray['FIO'] = $FIO;
        $var = 'birthday';
        $start_date=new \DateTime($returnArray[$var]);
        $returnArray[$var] = $start_date->format('d.m.Y');
        $var = 'data_create';
        $start_date=new \DateTime($returnArray[$var]);
        $returnArray[$var] = $start_date->format('d.m.Y');


        $this->data = $returnArray;
        return $returnArray;
    }

    public function getDataArray()
    {
        $conn = new \backend\Connection();

        $headArray = $this->getHeadArray();



        $ORG = $this->getORG();
        $query="
        SELECT        FIO.fam,FIO.im,FIO.ot,FIO.DOB as birthday
            FROM            dbo.FIO_journalRegistrations 
            inner join FIO
            ON 
            FIO_journalRegistrations.id_FIO = FIO.id AND
            FIO_journalRegistrations.id_LS = FIO.id_LS_propiska
            WHERE 
                  FIO_journalRegistrations.id_LS = {$this->data['id_LS']} AND
                  FIO.id_LS_propiska = {$this->data['id_LS']} AND 
                  FIO_journalRegistrations.ORG = {$ORG} AND 
                  FIO.ORG = {$ORG} AND
                  id_FIO <>  {$this->data['id_FIO']} AND 
                  dateReg <= convert(date,'{$this->data['dateUnReg']}') AND 
                  (dateUnReg > convert(date,'{$this->data['dateUnReg']}') OR dateUnReg is null)
            group by FIO.fam,FIO.im,FIO.ot,FIO.DOB
        ";
\models\ErrorLog::saveError($query,typeSaveMode: 'w+');

        $data = $conn->complexQuery($query);

        $transName = new \models\NameCaseLib\NCLNameCaseRu();

        $returnArray = Array();
        while ($row = $data->fetch()){
            $FIO =  $this->mb_strtoupper_first($row['fam'])." ".$this->mb_strtoupper_first($row['im'])." ".$this->mb_strtoupper_first($row['ot']);
            $row['FIO'] = $FIO;

            $var = 'birthday';
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