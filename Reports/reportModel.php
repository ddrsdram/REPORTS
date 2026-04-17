<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 05.06.2020
 * Time: 10:04
 */

namespace Reports;


abstract class reportModel
{
    public $id_report;
    public $conn;

    public $nameReport;
    public $extensionName;

    public $dataReports_Array;
    function __construct($id_report)
    {
        $this->id_report = $id_report;
        $this->conn = new \backend\Connection();
        $this->getDataReport();
    }

    private function getDataReport()
    {
        $this->dataReports_Array = $this->conn->table('reports_register')
            ->where('id',$this->id_report)
            ->select()->fetch();
    }


    public function setIdReport($id_report)
    {
        $this->id_report = $id_report;
        return $this;
    }

    public function getBodyByQuery()
    {
        //$returnArray = Array();
        $query = $this->dataReports_Array['query'];
        return $this->conn->ComplexQuery($query)->fetchAll();
        /*
        while ($row = $data->fetch()){
            $returnArray[] = $row;
        }

        return $returnArray;
        */
    }

    public function getBodyTextOnly()
    {
        return $this->dataReports_Array['query'];
    }


    public function getORG()
    {
        return $this->dataReports_Array['ORG'];
    }


    public function getHeadArray()
    {
        if (strlen($this->dataReports_Array['headArray'])>1)
            return json_decode($this->dataReports_Array['headArray'],true);
        else
            return Array();
    }

    public function getUser()
    {
        return $this->dataReports_Array['id_User'];
    }

    public function saveFileName()
    {

    }

    public function updateReports_register()
    {
        $this->conn->table("reports_register")
            ->set("fileName",$this->nameReport.$this->extensionName)
            ->where("id",$this->id_report)
            ->update();
    }

    public function getDataArray()
    {
        return Array();
    }

    /**
     * @return array
     */
    public function getAllDataHeadReport()
    {
        return $this->conn->table("reports_register")
            ->where("id",$this->id_report)
            ->select()->fetch();
    }
}