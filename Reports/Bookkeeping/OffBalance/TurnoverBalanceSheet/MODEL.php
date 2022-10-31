<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\Bookkeeping\OffBalance\TurnoverBalanceSheet;


class MODEL extends \Reports\reportModel
{
    public function getDataTable()
    {
        $data = $this->conn->table("View_off_balance_LsForReports_svod")
            ->where('id_user',$this->getUser())
            ->select();


        $returnArray = Array();

        while ($row = $data->fetch()){
            $returnArray[] = $row;
        }

        return $returnArray;

    }


}