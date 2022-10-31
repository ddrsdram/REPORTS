<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\IMPEXP\Download\AccrualsForMail;


class MODEL extends \Reports\reportModel
{
    public function getDataArray()
    {
        $H = $this->getHeadArray();
        return $this->conn->table("View_DAM_download_acruals_mail")
            ->where('region',$H['DataAccrualsForMailBank_id_region'])
            ->where('id_user',$this->getUser())
            ->select();
    }

}