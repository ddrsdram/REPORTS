<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\S_B_K\UPD;


class MODEL extends \Reports\reportModel
{


    public function getDataTable()
    {
        $array = $this->getHeadArray();


        return $array;
    }

}