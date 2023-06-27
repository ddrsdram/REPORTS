<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\PassportOffice\InOut;


use Mpdf\Tag\Th;

class MODEL extends \Reports\reportModel
{
    private $retArr         = Array();
    private $blockArr       = Array();
    private $head           = Array();

    private $arrivals       = Array();
    private $departures     = Array();
    private $allRegistr     = Array();
    private $allRegistrTmp  = Array();

    private $arrivals1      = Array();
    private $departures1    = Array();
    private $allRegistr1    = Array();
    private $allRegistrTmp1 = Array();

    private int $id_LS ;

    public function getDataTable()
    {
        $conn = new \backend\Connection();
        $head_array = $this->getHeadArray();
        $id_month = $head_array['id_month0'];
        $dateStart = $head_array['dateStart'];
        $dateEnd = $head_array['dateEnd'];
        $returnArray = Array();
        $ORG = $this->getORG();
        $id_user = $this->getUser();

        $data = $conn->table('View_REP_PassportOffice_InOut')
            ->where('ORG',$this->getORG())
           // ->where('id_month',$id_month)
            ->where('date_reg_arrivals',$dateStart,'>=')
            ->where('date_reg_arrivals',$dateEnd,'<=')
            ->orderBy('id_LS')
            ->groupBy("ORG, status_street, name_street, house, room, fam, im, ot, DOB, name_type_arrivals, address_arrivals, dateReg, name_type_departures, address_departures, dateUnReg, id_FIO, id_LS, id, id_month_arrivals, 
                         id_month_departures, day_unRegTrn, tmp, status, day_regTrn, date_reg_arrivals, date_reg_departures")
            ->select("ORG, status_street, name_street, house, room, fam, im, ot, DOB, name_type_arrivals, address_arrivals, dateReg, name_type_departures, address_departures, dateUnReg, id_FIO, id_LS, id, id_month_arrivals, 
                         id_month_departures, day_unRegTrn, tmp, status, day_regTrn, date_reg_arrivals, date_reg_departures")
            ->fetchAll();
        $this->arrivals = $this->transformArray($data);


        $data = $conn->table('View_REP_PassportOffice_InOut')
            ->where('ORG',$ORG)
          //  ->where('id_month',$id_month)
            ->where('date_reg_departures',$dateStart,'>=')
            ->where('date_reg_departures',$dateEnd,'<=')
            ->orderBy('id_LS')
            ->groupBy("ORG, status_street, name_street, house, room, fam, im, ot, DOB, name_type_arrivals, address_arrivals, dateReg, name_type_departures, address_departures, dateUnReg, id_FIO, id_LS, id, id_month_arrivals, 
                         id_month_departures, day_unRegTrn, tmp, status, day_regTrn, date_reg_arrivals, date_reg_departures")
            ->select("ORG, status_street, name_street, house, room, fam, im, ot, DOB, name_type_arrivals, address_arrivals, dateReg, name_type_departures, address_departures, dateUnReg, id_FIO, id_LS, id, id_month_arrivals, 
                         id_month_departures, day_unRegTrn, tmp, status, day_regTrn, date_reg_arrivals, date_reg_departures")
            ->fetchAll();
        $this->departures = $this->transformArray($data);


        $conn->table('list_LS_PassportOffice')
            ->where('id_user',$id_user)
            ->delete();
        $query = "
            insert into list_LS_PassportOffice
            SELECT        $id_month as id_month, ORG, id_LS, $id_user as id_user
                FROM            View_REP_PassportOffice_InOut
                WHERE (ORG = $ORG)  AND 
                (
                    ((date_reg_arrivals>='$dateStart') AND (date_reg_arrivals<='$dateEnd'))
                        OR
                    ((date_reg_departures>='$dateStart') AND (date_reg_departures<='$dateEnd'))
                )
                group by ORG, id_LS
        ";

        $conn->complexQuery($query);


        $this->head = $conn->table('View_REP_PassportOffice_InOut_head')
            ->where('ORG',$ORG)
            ->where('id_month',$id_month)
            ->where('id_user',$id_user)
            ->orderBy('status_street,name_street,house,room, id_LS')
            ->select()->fetchAll();
        //$this->head = $this->transformArray($data);


        $data = $conn->table('View_REP_PassportOffice_InOut_AllRegister')
            ->where('ORG',$ORG)
            ->where('id_month',$id_month)
            ->where('id_user',$id_user)
            ->orderBy('id_LS')
            ->select()->fetchAll();
        $this->allRegistr = $this->transformArray($data);


        $data = $conn->table('View_REP_PassportOffice_InOut_AllRegisterTmp')
            ->where('ORG',$ORG)
            ->where('id_month',$id_month)
            ->where('id_user',$id_user)
            ->orderBy('id_LS')
            ->select()->fetchAll();
        $this->addRegisterTMP($data);


        $row = 1;
        $this->createBlockArray();

        foreach ($this->head as $key => $item){
            $this->id_LS = (int) $item['id_LS'];

            $this->departures1      = (array)$this->departures[$this->id_LS];
            $this->arrivals1        = (array)$this->arrivals[$this->id_LS];
            $this->allRegistr1      = (array)$this->allRegistr[$this->id_LS];
            $this->allRegistrTmp1   = (array)$this->allRegistrTmp[$this->id_LS];

            $dep = true;
            $arr = true;
            $reg = true;
            $regTmp = true;

            $this->filling_M_data($row,$item);
            $row1 = 1;
            while ($dep || $arr || $reg){
                $dep = $this->filling_dep($row1);
                $arr = $this->filling_arr($row1);
                $reg = $this->filling_reg($row1);
                if ($dep || $arr || $reg)
                    $this->retArr[$item['id_LS']]['table1'][] = $this->blockArr;
                $row1 ++;
                $this->createBlockArray();
            }
            $row ++;

        }
        return $this->retArr;
    }


    private function filling_dep($row1) : bool
    {
        $ret =  false;
        if ($arr = current($this->departures1)){
            $ret =  true;
            $this->blockArr['dep_NN'] = $row1;
            $this->blockArr['dep_type_d'] = "{$arr['name_type_departures']}";
            $date = date("d.m.y",strtotime($arr['dateUnReg']));
            $this->blockArr['dep_date'] = $date;

            $date = date("d.m.y",strtotime($arr['DOB']));
            $this->blockArr['dep_FIO'] = "{$arr['fam']} {$arr['im']} {$arr['ot']}, $date д.р.";

            next($this->departures1);
        }

        return $ret;
    }

    private function filling_arr($row1) : bool
    {
        $ret =  false;
        if ($arr = current($this->arrivals1)){
            $ret =  true;
            $this->blockArr['arr_NN'] = $row1;
            $date = date("d.m.y",strtotime($arr['dateReg']));
            if ($arr['status'] == 'ВР'){
                $dateUn = date("d.m.y",strtotime($arr['dateUnReg']));
                $date = "Врем. с $date по $dateUn";
            }
            $this->blockArr['arr_date'] = $date;

            $date = date("d.m.y",strtotime($arr['DOB']));
            $this->blockArr['arr_FIO'] = "{$arr['fam']} {$arr['im']} {$arr['ot']}, $date д.р.";

            next($this->arrivals1);
        }

        return $ret;
    }

    private function filling_reg($row1) : bool
    {
        $ret =  false;
        if ($arr = current($this->allRegistr1)){
            $ret =  true;
            $this->blockArr['NN1'] = $row1;

            $date = date("d.m.y",strtotime($arr['DOB']));
            $this->blockArr['FIO'] = "{$arr['fam']} {$arr['im']} {$arr['ot']}, $date д.р.";
            $status = 'Постоянная прописка';
            if (array_key_exists('status',$arr)){
                $date = date("d.m.y",strtotime($arr['dateReg']));
                $dateUn = date("d.m.y",strtotime($arr['dateUnReg']));
                $status = "Врем. с $date по $dateUn";
            }
            $this->blockArr['status'] = $status;
            next($this->allRegistr1);
        }

        return $ret;
    }





    /**
     * @param array $data
     * @return array
     */
    private function transformArray(array $data) : array
    {
        $id_LS = false;
        $retData = Array();
        $rowData = Array();
        foreach ($data as $key => $item){
            if ($id_LS === false)
                $id_LS = $item['id_LS'];

            if ($id_LS != $item['id_LS']){
                $retData[$id_LS] = $rowData;

                $rowData = Array();
                $id_LS = $item['id_LS'];
            }
            $rowData[] = $item;
        }
        $retData[$id_LS] = $rowData;
        return $retData;
    }

    /**
     * @param array $data

     */
    private function addRegisterTMP(array $data)
    {
        foreach ($data as $key => $item) {
            $id_LS = $item['id_LS'];
            $this->allRegistr[$id_LS][] = $item;
        }

    }


    private function filling_M_data($row,$arr)
    {
        $this->blockArr['m_NN'] = $row;
        $this->blockArr['m_Address'] = "ЛС:{$arr['id_LS']}: {$arr['status_street']} {$arr['name_street']}, д.{$arr['house']}, кв.{$arr['room']}";
        $date = date("d.m.y",strtotime($arr['DOB']));
        $this->blockArr['m_FIO'] = "{$arr['fam']} {$arr['im']} {$arr['ot']}, $date д.р.";
    }


    private function createBlockArray()
    {
        $this->blockArr = Array(
            "m_NN" => "",
            "m_Address" => "",
            "m_FIO" => "",


            "dep_NN" => "",
            "dep_FIO" => "",
            "dep_type_d" => "",
            "dep_date" => "",

            "arr_NN" => "",
            "arr_FIO" => "",
            "arr_date" => "",

            "NN1" => "",
            "FIO" => "",
            "status" => "",

        );
    }
}