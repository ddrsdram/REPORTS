<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\RSO\Prokopevsk\Hot_pSector;


class MODEL extends \Reports\reportModel
{

    public function getDataTable()
    {
        $conn = new \backend\Connection();
        $conn->table('month_decoding_reports')
            ->where('id_user', $this->getUser())
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

        $data = $conn->table('View_REP_RSO_Prokopevsk_hot_privateArea')
            ->where('id_user', $this->getUser())
            ->where('ORG', $this->getORG())
            ->orderBy("id_month")
            ->select()->fetchAll();
        $returnArray = array();

        foreach ($data as $key => $t1) {
            $returnArray[$t1['id_month'] . '_0']['name_month'] = $t1['name_month'];
            $returnArray[$t1['id_month'] . '_0']['id_month'] = $t1['id_month'];
            $returnArray[$t1['id_month'] . '_0']['standard'] = $t1['standard'];

            $returnArray[$t1['id_month'] . '_0']['kol_house'] = $t1['kol_house'];

            $returnArray[$t1['id_month'] . '_0']['value45'] = $t1['value4'];

            $returnArray[$t1['id_month'] . '_0']['summa'] = $t1['summa1'];
            $returnArray[$t1['id_month'] . '_0']['summa12'] = $t1['summa1'] + $t1['summa2'];

            $returnArray[$t1['id_month'] . '_0']['tarif'] = $t1['tarif1'];
            if ($t1['summa2'] != 0) {
                $returnArray[$t1['id_month'] . '_1']['name_month'] = $t1['name_month'];
                $returnArray[$t1['id_month'] . '_1']['id_month'] = $t1['id_month'];
                $returnArray[$t1['id_month'] . '_1']['standard'] = $t1['standard'];

                $returnArray[$t1['id_month'] . '_1']['kol_house'] = '';

                $returnArray[$t1['id_month'] . '_1']['value45'] = $t1['value5'];

                $returnArray[$t1['id_month'] . '_1']['summa'] = $t1['summa2'];
                $returnArray[$t1['id_month'] . '_1']['summa12'] = $t1['summa1'] + $t1['summa2'];

                $returnArray[$t1['id_month'] . '_1']['tarif'] = $t1['tarif2'];
            }

        }
        return $returnArray;    }

}