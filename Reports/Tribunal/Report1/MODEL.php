<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\Tribunal\Report1;


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

    public function getDataTable($idUser)
    {
        $conn = new \backend\Connection();
        $conn->table('View_tribunal_REPORT')
            ->where('id_user',$idUser)
            ->orderBy('name_JEU,name_street,house,room');

        if ($this->AllRuling == '0')
            $conn->where('closed','0');

        $data = $conn->select();
        $returnArray = $data->fetchAll();
        return $returnArray;
    }

}