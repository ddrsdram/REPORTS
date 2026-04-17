<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\Device\ExportDeviceForSBER;

use DB\View\View_deviceForExportSBER;

class MODEL extends \Reports\reportModel
{
    public function getDataArray()
    {
        $H = $this->getHeadArray();
        $this->conn->table("View_DAM_download_acruals_mail")
            ->where('id_user',$this->getUser());

        if ($H['AllRegionInToOneFile'] != '1')
            $this->conn->where('region',$H['DataAccrualsForSBER_id_region']);

        return $this->conn->select();
    }

    public function getDevice()
    {
        $H = $this->getHeadArray();
        $d = new View_deviceForExportSBER();
        $data = $d
            ->where($d::ORG,$this->getORG())
            ->where($d::id_month,$H['id_month'])
            ->orderBy($d::id_LS)->select();

        $retData = Array();
        while ($res = $data->fetch()){
            if (!array_key_exists($res[$d::id_LS],$retData)){
                $retData[$res[$d::id_LS]] = Array();
            }
            $retData[$res[$d::id_LS]][] = $res;
        }
        return $retData;
    }
}