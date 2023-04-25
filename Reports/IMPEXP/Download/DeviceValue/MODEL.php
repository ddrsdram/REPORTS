<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\IMPEXP\Download\DeviceValue;


class MODEL extends \Reports\reportModel
{
    public function getDataArray()
    {
        $conn = new \DB\Connect();
        return $conn->table('View_IMPEXP_deviceValue')
            ->where('ORG',$this->getORG())
            ->select()->fetchAll();
    }
}