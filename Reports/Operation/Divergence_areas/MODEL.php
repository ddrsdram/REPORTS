<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\Operation\Divergence_areas;


class MODEL extends \Reports\reportModel
{
    public function get_Divergence_areas()
    {
        $conn = new \DB\Connect();
        $headArray = $this->getHeadArray();
        $id_month = $headArray['id_month'];
        $id_month1 = $id_month - 1;
        $ORG = $this->getORG();
        $query = "
        SELECT        now.id_month, now.ORG, street.status, street.name  as name_street, now.house, now.room, now.id_LS, now.id_global_LS, now.area, old.area AS old_area
            FROM            (
                                SELECT        id_month, ORG, id_LS, id_global_LS, area, id_street, house, room
                                FROM            LS_head
                                WHERE        (id_month = $id_month) AND (ORG = $ORG) AND (id_LS <> 0) AND (LS_head.id_month_closed = 0)
                ) AS now 
            INNER JOIN
                             (
                                 SELECT        id_month, ORG, id_LS, id_global_LS, area
                                    FROM            LS_head AS LS_head_1
                                    WHERE        (id_month = $id_month1) AND (ORG = $ORG) AND (id_LS <> 0)
                             ) AS old 
                ON 
                    now.ORG = old.ORG AND now.id_LS = old.id_LS AND now.area <> old.area 
            INNER JOIN street 
                ON 
                    now.id_street = street.id
        ";
        \models\ErrorLog::saveError($query,typeSaveMode: 'w+');

        return $conn->complexQuery($query)->fetchAll();
    }

    public function get_Divergence_areas_close()
    {
        $conn = new \DB\Connect();
        $headArray = $this->getHeadArray();
        $id_month = $headArray['id_month'];
        $ORG = $this->getORG();
        $query = "
        SELECT        now.id_month, now.ORG, street.status, street.name  as name_street, now.house, now.room, now.id_LS, now.id_global_LS, now.area, old.area AS old_area
        FROM            (
                            SELECT        id_month, ORG, id_LS, id_global_LS, area, id_street, house, room
                            FROM            LS_head
                            WHERE        (id_month = $id_month) AND (ORG = $ORG) AND (id_LS <> 0)
                        ) AS now 
        INNER JOIN
                        (
                            SELECT        LS_head.id_month, LS_head.ORG, LS_head.id_LS, LS_head.id_global_LS, LS_o.area
							FROM            LS_head INNER JOIN
													 LS_head AS LS_o ON LS_head.ORG = LS_o.ORG AND LS_head.id_LS = LS_o.id_LS AND LS_head.id_month - 1 = LS_o.id_month
							WHERE        (LS_head.id_LS <> 0) AND (LS_head.id_month = $id_month) AND (LS_head.ORG = $ORG) AND (LS_head.id_month_closed = $id_month)
                        ) AS old 
            ON 
                now.ORG = old.ORG AND now.area <> old.area AND now.id_global_LS = old.id_global_LS AND now.id_LS > old.id_LS 
        INNER JOIN street 
            ON 
                now.id_street = street.id
        ";
        \models\ErrorLog::saveError($query);
        return $conn->complexQuery($query)->fetchAll();
    }
}