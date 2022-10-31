<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\Device\Register_averaging;


class MODEL extends \Reports\reportModel
{
    private $sumColumn;
    private $JEU_Array;
    private $typeAccrual_Array;

    function __construct($id_report)
    {
        parent::__construct($id_report);
        $this->sumColumn = 0;
    }

    public function getDataTable($idUser)
    {

        $conn = new \backend\Connection();
        return $conn->table('View_REP_device_list_averaging')
            //->where("ORG",$ORG)
            ->where("id_user",$idUser)
            ->orderBy('name_JEU,name_street,house,room')
            ->select()
            ->fetchAll();

    }

}