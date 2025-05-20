<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\IMPEXP\Download\Export_Gorset;


class MODEL extends \Reports\reportModel
{
    public function getDataArray()
    {
        $d = new \DB\Proc\Proc_export_Gorset();
        $d->parameters($this->getORG(),$this->getHeadArray()['id_month_this']);
        $d = new \DB\Table\import_Gorset();
        $data = $d->where($d::ORG,$this->getORG());


        return $data->select()->fetchAll();
    }
}