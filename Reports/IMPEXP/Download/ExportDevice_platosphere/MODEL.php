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
        // пришло с другой модели
        $this->conn->table("View_exportDevice_platosphere")
            ->where('ORG',$this->getORG())
            ->where('id_month',$H['id_month']);

        if ($H['AllRegionInToOneFile'] != '1')
            $this->conn->where('region',$H['DataExports_id_region']);

        $this->conn->orderBy("region , id_LS");
        \models\ErrorLog::saveError($H,typeSaveMode: 'w+');
        \models\ErrorLog::saveError($this->conn);
        return $this->conn->select()->fetchAll();
    }


}