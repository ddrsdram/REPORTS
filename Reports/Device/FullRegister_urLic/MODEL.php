<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\Device\FullRegister_urLic;


use DB\Connect;
use DB\Connection;
use DB\View\View_REP_device_list_UrLic;

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

        $d = new View_REP_device_list_UrLic();
        $conn = new Connect();
        return $conn->complexQuery(/** lang SQL */" 
        SELECT * from View_REP_device_list_UrLic
        WHERE 
            id_user = $idUser AND id_type_accrual IN (7,8)
        ORDER BY name_JEU,id_LS_global,name_street,house_int,room_int,sorting
        ")->fetchAll();
    }

}