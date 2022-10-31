<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\IMPEXP\Download\AccrualsForSBER;


class MODEL extends \Reports\reportModel
{
    public function getDataArray()
    {
        $H = $this->getHeadArray();
        return $this->conn->table("View_DAM_download_acruals_mail")
            ->where('region',$H['DataAccrualsForSBER_id_region'])
            ->where('id_user',$this->getUser())
            ->select();
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