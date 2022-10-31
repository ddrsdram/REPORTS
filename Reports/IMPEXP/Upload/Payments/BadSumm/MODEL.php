<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\IMPEXP\Upload\Payments\BadSumm;


class MODEL extends \Reports\reportModel
{
    public function getDataArray()
    {

        return $this->getBodyByQuery();
    }
}