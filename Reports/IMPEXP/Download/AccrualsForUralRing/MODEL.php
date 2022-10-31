<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\IMPEXP\Download\AccrualsForUralRing;


class MODEL extends \Reports\reportModel
{
    public function getDataArray()
    {
        return $this->conn->table("View_DAM_download_acruals_mail")
            ->where('id_user',$this->getUser())
            ->select();
    }
}