<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\IMPEXP\Download\AccrualsForSBER_GISJKH_TSG;


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

    public function getExtensionForRegion($id_region)
    {
        $conn = new  \backend\Connection();
        return  $conn->table('requisitesForRegion')
            ->where('id_region',$id_region)
            ->where('ORG',$this->getORG())
            ->select('extension_SBER')
            ->fetchField('extension_SBER');
    }
}