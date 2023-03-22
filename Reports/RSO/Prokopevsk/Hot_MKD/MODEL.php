<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\RSO\Prokopevsk\Hot_MKD;


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

        $returnArray = $conn->table('View_REP_RSO_Prokopevsk_hot_MKD_1')
            ->where('id_user', $this->getUser())
            ->where('ORG', $this->getORG())
            ->orderBy("id_month")
            ->select()->fetchAll();

        return $returnArray;
    }

}