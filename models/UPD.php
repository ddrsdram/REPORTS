<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 23.09.2021
 * Time: 23:41
 */

namespace models;


class UPD
{
    private $ORG;
    private $idUPD;
    private $sum;
    private $nameAIS;
    private $thisMonth_1;
    private $thisMonth_2;
    private $Contract_num;
    private $Contract_date;

    public function defineNameMonth($nomMonth)
    {
        switch ($nomMonth){
            case 1 :
                $this->thisMonth_1 = 'январь';
                $this->thisMonth_2 = 'января';
                break;
            case 2 :
                $this->thisMonth_1 = 'февраль';
                $this->thisMonth_2 = 'февраля';
                break;
            case 3 :
                $this->thisMonth_1 = 'март';
                $this->thisMonth_2 = 'марта';
                break;
            case 4 :
                $this->thisMonth_1 = 'апрель';
                $this->thisMonth_2 = 'апреля';
                break;
            case 5 :
                $this->thisMonth_1 = 'май';
                $this->thisMonth_2 = 'мая';
                break;
            case 6 :
                $this->thisMonth_1 = 'июнь';
                $this->thisMonth_2 = 'июня';
                break;
            case 7 :
                $this->thisMonth_1 = 'июль';
                $this->thisMonth_2 = 'июля';
                break;
            case 8 :
                $this->thisMonth_1 = 'август';
                $this->thisMonth_2 = 'августа';
                break;
            case 9 :
                $this->thisMonth_1 = 'сентябрь';
                $this->thisMonth_2 = 'сентября';
                break;
            case 10 :
                $this->thisMonth_1 = 'октябрь';
                $this->thisMonth_2 = 'октября';
                break;
            case 11 :
                $this->thisMonth_1 = 'ноябрь';
                $this->thisMonth_2 = 'ноября';
                break;
            case 12 :
                $this->thisMonth_1 = 'декабрь';
                $this->thisMonth_2 = 'декабря';
                break;
        }
    }

    /**
     * @param mixed $Contract_num
     */
    public function setContractNum($Contract_num)
    {
        $this->Contract_num = $Contract_num;
    }

    /**
     * @param mixed $Contract_date
     */
    public function setContractDate($Contract_date)
    {
        $this->Contract_date = $Contract_date;
    }
    /**
     * @param mixed $nameAIS
     */
    public function setNameAIS($nameAIS)
    {
        $this->nameAIS = $nameAIS;
    }


    /**
     * @param mixed $idUPD
     */
    public function setIdUPD($idUPD)
    {
        $this->idUPD = $idUPD;
    }

    /**
     * @param mixed $sum
     */
    public function setSum($sum)
    {
        $this->sum = $sum;
    }


    /**
     * @param mixed $ORG
     */
    public function setORG($ORG)
    {
        $this->ORG = $ORG;
    }

    public function create()
    {
        $report = new  \models\Reports();
        $report->setWait(1);
        $report->prepareReport('\Reports\S_B_K\UPD');
        $this->prepareData($report);

        $report->runCreateReport();

        return $report->getGUIDReport();
    }

    /**
     * @param $classReports \models\Reports
     */
    public function prepareData($classReports)
    {
        $security = new \DB\Connect(\properties\security::GD);
        $arrayConnectionSettings = $security->table("ORG")
            ->where("ORG",$this->ORG)
            ->select("serverName, [dataBase], userName, password")->fetch();
        $conn = new \backend\Connection($arrayConnectionSettings);
        /*
         * Подготовка Шапки
         */
        $dataHead = Array();
        $dataHead = $conn->table('View_main_properties')
            ->where('id_month',$_SESSION['id_month0'])
            ->where('ORG',$this->ORG)
            ->select()->fetch();
        $nom_month = (int) $dataHead['nom_month'] + 1;
        $nom_month = $nom_month == 13 ? 1 : $nom_month;
        $this->defineNameMonth($nom_month);

        $dataHead['idUPD']          =  $this->idUPD;
        $dataHead['sum']            =  $this->sum;
        $dataHead['ORG']            =  $this->ORG;
        $dataHead['nameAIS']        =  $this->nameAIS;
        $dataHead['thisMonth_1']    =  $this->thisMonth_1 ;
        $dataHead['thisMonth_2']    =  $this->thisMonth_2 ;
        $dataHead['thisYear']       =  date("Y") ;
        $dataHead['Contract_num']   =  $this->Contract_num ;
        $dataHead['Contract_date']  =  $this->Contract_date ;

        $classReports->headReport($dataHead);

    }
}