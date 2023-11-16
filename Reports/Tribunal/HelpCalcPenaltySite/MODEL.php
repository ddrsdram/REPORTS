<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\Tribunal\HelpCalcPenaltySite;


class MODEL extends \Reports\reportModel
{
    private $AllRuling = '0';

    /**
     * @param string $AllRuling
     */
    public function setAllRuling(string $AllRuling): void
    {
        $this->AllRuling = $AllRuling;
    }

    public function getSumAccrual()
    {
        return $this->getDataTable(1);
    }
    public function getSumPayment()
    {
        return $this->getDataTable(-1);
    }


    private function getDataTable($DK)
    {
        $conn = new \backend\Connection();
        $idUser = $this->getUser();
        $ORG = $this->getORG();

        $conn->table('Penalty_summ_main')
            ->where('id_user',$idUser)
            ->where('ORG',$ORG)
            ->where('DK',$DK)
            ->orderBy('dateOpiration');
        $data = $conn->select("dateOpiration,ABS(summa) as summa, DK");
        $returnArray = $data->fetchAll();
        return $returnArray;
    }

}