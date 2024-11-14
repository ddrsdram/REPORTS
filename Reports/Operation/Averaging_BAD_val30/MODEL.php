<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\Operation\Averaging_BAD_val30;


class MODEL extends \Reports\reportModel
{
    public function getData()
    {
        $conn = new \DB\Connect();
        $ORG = $this->getORG();
        $query = "
        SELECT        
               Averaging_value_device_rep30.ORG, 
               Averaging_value_device_rep30.id_device, 
               type_accrual.name, region.name AS name_region, 
               street.status AS status_street, street.name AS name_street, 
               global_LS.house, global_LS.room,                          
               device.data_install, device.dala_start,
               device.data_test_device, device.date_sealing, 
               device.year_job, device.factory_number, 
               device.mark, device.model,
               device.value_start,
               device.value_end, 
               device.name AS name_device, 
               device.Averaging_off
        FROM           
             Averaging_value_device_rep30 
        INNER JOIN
                 device 
                     ON 
                         Averaging_value_device_rep30.ORG = device.ORG AND 
                         Averaging_value_device_rep30.id_device = device.id 
        INNER JOIN
                 type_accrual 
                     ON
                         device.ORG = type_accrual.ORG AND 
                         device.id_type_accrual = type_accrual.id 
        INNER JOIN
                 global_LS
                     ON 
                         device.id_LS_global = global_LS.id 
        INNER JOIN
                 street 
                     ON 
                         global_LS.id_street = street.id 
        INNER JOIN
                 region 
                     ON 
                         global_LS.region = region.id
        WHERE        
              (Averaging_value_device_rep30.ORG = $ORG)
        ";

        return $conn->complexQuery($query)->fetchAll();
    }


}