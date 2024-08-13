<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\IMPEXP\Download\ExportDevice_platosphere;


class MODEL extends \Reports\reportModel
{
    public function getDataArray()
    {
        $H = $this->getHeadArray();


        $this->conn->table("month_platosphere")
            ->where("id_user",$this->getUser())
            ->where("ORG",$this->getORG())
            ->delete();
        $this->conn->table("month_platosphere")
            ->set("id_user",$this->getUser())
            ->set("ORG",$this->getORG())
            ->set("id_month",$H['id_month'])
            ->insert();
        // пришло с другой модели

        $this->conn->table("View_exportDevice_platosphere")
            ->where("id_user",$this->getUser())
            ->where('ORG',$this->getORG())
            ->where('id_month',$H['id_month']);

        if ($H['AllRegionInToOneFile'] != '1')
            $this->conn->where('region',$H['DataExports_id_region']);

        $this->conn->orderBy("region , id_LS");
        return $this->conn->select()->fetchAll();
    }


}