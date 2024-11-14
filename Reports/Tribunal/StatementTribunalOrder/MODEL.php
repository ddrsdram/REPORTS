<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\Tribunal\StatementTribunalOrder;


use models\ErrorLog;

class MODEL extends \Reports\reportModel
{
    private $dataArray;
    private $fio_summ;

    /**
     * @return mixed
     */
    public function getFioSumm()
    {
        return $this->fio_summ;
    }

    public function getDataArray()
    {
        $conn = new \backend\Connection();
        $this->fio_summ = '';

        $this->dataArray = Array();
        $data = $conn->table('View_REP_FIO_Certificate_1')
            ->where('id_user',$this->getUser())
            ->select();

        $row = $data->fetch();
        $this->setRow($row);

        $headArray = $this->getHeadArray();


        $conn->table('View_REP_FIO_Certificate_All')
            ->where('id_user',$this->getUser())
            ->where('ORG',$this->getORG());

        if (array_key_exists('ChildrenOff',$headArray)){
            if ($headArray['ChildrenOff'] == '0')
                $conn->where('years',18,'>=');
        }

        $data = $conn->select();

        while ($row = $data->fetch()){
            $this->setRow($row);
        }

        return $this->dataArray;
    }


    public function getHeadArray()
    {
        if (strlen($this->dataReports_Array['headArray'])>1){
            $retArr = Array();

            $retArr = json_decode($this->dataReports_Array['headArray'],true);
            $retArr['FIOSumm'] = $this->getFioSumm();
            $retArr = $this->addHeadArray($retArr);
            return $retArr;
        }
        else
            return Array();
    }



    private function addHeadArray($retArr)
    {
        $conn = new \backend\Connection();
        $data = $conn->table('View_tribunal_Order_head')
            ->where('id_user',$this->getUser())
            ->where('ORG',$this->getORG())
            ->select()->fetch();
        $addressHouse = ($data['address_region'] . ', ');
        $addressHouse .= ($data['status_street'] . ' ');
        $addressHouse .= $this->mb_strtoupper_first($data['name_street']) . ', ';
        $addressHouse .= ('д.' . $data['house'] . ', ');
        $addressHouse .= ('кв.' . $data['room']);
        $retArr['addressHouse'] = $addressHouse;
        $retArr['accrual'] = (float) $data['accrual'];
        $retArr['GosPoshlina'] = (float) str_replace(',','.',$retArr['GosPoshlina']);
        $retArr['GosPoshlina2'] = (float) str_replace(',','.',$retArr['GosPoshlina2']);
        $retArr['SumPenalty'] = (float) str_replace(',','.',$retArr['SumPenalty']);
        $retArr['PenaltyOff'] = (int)$retArr['PenaltyOff'];

        $retArr['summa'] = $retArr['accrual'] + $retArr['GosPoshlina'] + ($retArr['PenaltyOff'] * $retArr['SumPenalty']);
        $retArr['text'] = \models\Num2Str::getText($retArr['summa']);

        $dateTxt = "{$data['year_month_start']}-{$data['nom_month_start']}-01";
        $dateTime = strtotime($dateTxt);
        $newDateTxt= \models\dateRUS::get('"d" F Y ',$dateTime,1);
        $retArr['dateStart'] = $newDateTxt;
        //$retArr['dateStart'] = $dateTxt;

        $dateTxt = "{$data['year_month_end']}-{$data['nom_month_end']}-{$data['lastDay_month_end']}";
        $dateTime = strtotime($dateTxt);
        $newDateTxt = \models\dateRUS::get('"d" F Y ',$dateTime,1);
        $retArr['dateEnd'] = $newDateTxt;
        //$retArr['dateEnd'] = $dateTxt;


        $data = $conn->table('proc_Penalty_get_calculateForOneLS')
            ->set('id_user',$this->getUser())
            ->set('ORG',$this->getORG())
            ->set('id_LS',$retArr['id_LS'])
            ->SQLExec();
        $Arr = $data->fetch();
        $dateTime = strtotime($Arr['id_date']);
        $retArr['dateStartPenalty'] = \models\dateRUS::get('"d" F Y ',$dateTime,1);

        return $retArr;
    }



    private function setRow($row)
    {
        $transName = new \models\NameCaseLib\NCLNameCaseRu();

        $FIO =  $this->mb_strtoupper_first($row['fam'])." ".$this->mb_strtoupper_first($row['im'])." ".$this->mb_strtoupper_first($row['ot']);
        $row['FIO'] = $FIO;

        //$FIO_case =  $transName->q($FIO, \models\NameCaseLib\NCL\NCL::$RODITLN);
        $FIO_case =  $FIO;
        $row['FIO_case'] = $FIO_case;
        $this->fio_summ .= ' ' . $FIO_case . ', ';

        $gender = $transName->genderDetect($FIO);
        if ($gender == \models\NameCaseLib\NCL\NCL::$MAN){
            $row['native'] = 'уроженец';
        }else{
            $row['native'] = 'уроженка';
        }



        $var = 'birthday';
        $start_date=new \DateTime($row[$var]);
        $row[$var] = $start_date->format('d.m.Y');

        $var = 'data_create';
        $start_date=new \DateTime($row[$var]);
        $row[$var] = $start_date->format('d.m.Y');

        $this->dataArray[] = $row;

    }

    private function mb_strtoupper_first($str, $encoding = 'UTF8')
    {
        $str = mb_strtolower($str, $encoding);
        return
            mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding) .
            mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
    }

    public function getSettingsTribunal()
    {
        $d = new \DB\Table\tribunal_settings();
        return $d->where($d::ORG,$this->getORG())
            ->select()->fetch();
    }
}