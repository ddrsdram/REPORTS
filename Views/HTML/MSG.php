<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 23.09.2021
 * Time: 15:57
 */

namespace Views\HTML;


abstract class MSG
{
    public $FIO;
    public $gender;
    public $thisMonth_1;
    public $thisMonth_2;
    public $Contract_num;
    public $Contract_date;
    public $sum;
    public $nameAIS;

    function __construct($FIO)
    {
        $nc = new \models\NameCaseLib\NCLNameCaseRu();
        $gender = $nc->genderDetect($FIO);
        $this->FIO = $FIO;

        if ($gender == \models\NameCaseLib\NCL\NCL::$MAN){
            $this->gender    = 'Уважаемый';
        }else{
            $this->gender    = 'Уважаемая';
        }
        switch (date("m")){
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


    abstract function getMessage();

    /**
     * @param mixed $sum
     */
    public function setSum($sum)
    {
        $this->sum = $sum;
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
}