<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\RSO\Prokopevsk\HotWater_and_HOT;


use Mpdf\Tag\Th;

class MODEL extends \Reports\reportModel
{

    public function setProperties()
    {
        $conn = new \backend\Connection();
        $conn->table('month_decoding_reports')
            ->where('id_user',$this->getUser())
            ->delete();
        $id_user = $this->getUser();
        //Добавление максимального и мимнимального месяца для отчета
        $query = "
        INSERT INTO dbo.month_decoding_reports(id_month_old,id_month_now,ORG,id_user)
        SELECT        MIN(month_1.id) AS id_month_old, id_month_now.id_month, id_month_now.ORG, id_month_now.id_user
            FROM            (SELECT        MAX(id_month) AS id_month, ORG, id_user
                                      FROM            dbo.list_LS_decoding_reports
                                      WHERE id_user = $id_user
                                      GROUP BY ORG, id_user
                ) AS id_month_now INNER JOIN
                                     dbo.month ON id_month_now.id_month = dbo.month.id INNER JOIN
                                     dbo.month AS month_1 ON dbo.month.year = month_1.year
            GROUP BY id_month_now.id_month, id_month_now.ORG, id_month_now.id_user
        ";
        $conn->complexQuery($query);

    }
    public function getDataTable_HotWater()
    {

        $conn = new \backend\Connection();
        $data = $conn->table('View_REP_RSO_Prokopevsk_hot_water_svod')
            ->where('id_user',$this->getUser())
            ->where('ORG',$this->getORG())
            ->orderBy("id_month")
            ->select()->fetchAll();

        $returnArray = Array();
        $value4 = 0;
        $value5 = 0;
        $sum1 = 0;
        $sum2 = 0;
        foreach ($data as $key => $t1){
            $value4 += $t1['value4'];
            $value5 += $t1['value5'];
            $sum1 += $t1['summa1'];
            $sum2 += $t1['summa2'];
        }

        $returnArray[$t1['id_month'].'0']['name_month']     = $t1['name_month'];
        $returnArray[$t1['id_month'].'0']['id_month']       = $t1['id_month'];
        $returnArray[$t1['id_month'].'0']['name']           = $t1['name'];

        $returnArray[$t1['id_month'].'0']['recalc']         = $t1['recalc'];
        $returnArray[$t1['id_month'].'0']['summa12']        = $t1['summa1'];
        $returnArray[$t1['id_month'].'0']['value45']        = $t1['value4'];
        $returnArray[$t1['id_month'].'0']['tarif']          = $t1['tarif1'];

        $returnArray[$t1['id_month'].'0']['summa12_sum']    = $sum1;
        $returnArray[$t1['id_month'].'0']['value45_sum']    = $value4;
        if ($t1['summa2'] != 0){
            $returnArray[$t1['id_month'].'1']['name_month']     = '';
            $returnArray[$t1['id_month'].'1']['id_month']       = $t1['id_month'];
            $returnArray[$t1['id_month'].'1']['name']           = '';

            $returnArray[$t1['id_month'].'1']['recalc']         = 0;
            $returnArray[$t1['id_month'].'1']['summa12']        = $t1['summa2'];
            $returnArray[$t1['id_month'].'1']['value45']        = $t1['value5'];
            $returnArray[$t1['id_month'].'1']['tarif']          = $t1['tarif2'];
            $returnArray[$t1['id_month'].'1']['summa12_sum']    = $sum2;
            $returnArray[$t1['id_month'].'1']['value45_sum']    = $value5;
        }
        return $returnArray;
    }

    public function getDataTable_Hot()
    {

        $conn = new \backend\Connection();
        $data = $conn->table('View_REP_RSO_Prokopevsk_hot_svod')
            ->where('id_user',$this->getUser())
            ->where('ORG',$this->getORG())
            ->orderBy("id_month,id")
            ->select()->fetchAll();

        $returnArray = Array();
        foreach ($data as $key => $t1){


            if (array_key_exists('summa12_sum',(array) $returnArray[$t1['id'].'0']) === false){
                $returnArray[$t1['id'].'0']['summa12_sum'] = 0;
                $returnArray[$t1['id'].'1']['summa12_sum'] = 0;
            }
            if (array_key_exists('value45_sum',(array)$returnArray[$t1['id'].'0']) === false){
                $returnArray[$t1['id'].'0']['value45_sum'] = 0;
                $returnArray[$t1['id'].'1']['value45_sum'] = 0;
            }


            $returnArray[$t1['id'].'0']['name_month']     = $t1['name_month'];
            $returnArray[$t1['id'].'0']['name']           = $t1['name'];

            $returnArray[$t1['id'].'0']['recalc']         = $t1['recalc'];
            $returnArray[$t1['id'].'0']['summa12']        = $t1['summa1'];
            $returnArray[$t1['id'].'0']['value45']        = $t1['value4'];
            $returnArray[$t1['id'].'0']['tarif']          = $t1['tarif1'];

            $returnArray[$t1['id'].'0']['summa12_sum']    += $t1['summa1'];
            $returnArray[$t1['id'].'0']['value45_sum']    += $t1['value4'];
            if ($t1['summa2'] != 0){
                $returnArray[$t1['id'].'1']['name_month']     = '';
                $returnArray[$t1['id'].'1']['id']       = $t1['id'];
                $returnArray[$t1['id'].'1']['name']           = '';

                $returnArray[$t1['id'].'1']['recalc']         = 0;
                $returnArray[$t1['id'].'1']['summa12']        = $t1['summa2'];
                $returnArray[$t1['id'].'1']['value45']        = $t1['value5'];
                $returnArray[$t1['id'].'1']['tarif']          = $t1['tarif2'];
                $returnArray[$t1['id'].'1']['summa12_sum']    += $t1['summa2'];
                $returnArray[$t1['id'].'1']['value45_sum']    += $t1['value5'];
            }
        }


        return $returnArray;
    }




}