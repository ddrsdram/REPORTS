<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\backUpForORG;


class MODEL extends \Reports\reportModel
{

    public function getDataTable()
    {
        $conn = new \backend\Connection();

        $headArray = $this->getHeadArray();
        $conn->table($headArray['tableName'])
            ->where('ORG',$this->getORG());

        if ($headArray['whereMonth'] == "1")
            $conn->where('id_month', $headArray['id_month']);

        return $conn->select();
    }

}