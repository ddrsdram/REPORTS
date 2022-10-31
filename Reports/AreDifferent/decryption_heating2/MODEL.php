<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\AreDifferent\decryption_heating2;


class MODEL extends \Reports\reportModel
{

    public function getDataTable()
    {
        $conn = new \backend\Connection();
        $data = $conn->table('View_REP_decryption_heating2_byHouse')
            ->where('id_user',$this->getUser())
            ->orderBy('name_tarif, device, name_street,house_int,house')
            ->select()
            ->fetchAll();
        $returnArray = Array();
        $returnArray['count_ST'] = 0;
        $returnArray['count_OTH'] = 0;
        $returnArray['count_DEV'] = 0;
        $otherTariffs = 0;
        foreach ( $data as $key => $row ){
            if ($row['device'] == '0' ){
                if ($otherTariffs == 0){
                    $returnArray['ST'][] = $row;
                    $returnArray['count_ST'] ++;
                }

                else{
                    $returnArray['OTH'][] = $row;
                    $returnArray['count_OTH'] ++;
                }

            }else{
                $otherTariffs = 1;
                $returnArray['DEV'][] = $row;
                $returnArray['count_DEV'] ++;
            }
        }

        return $returnArray;
    }

}