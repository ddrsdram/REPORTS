<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 23.09.2021
 * Time: 23:41
 */

namespace models\backUpForORG;


class CreateFile
{

    private $tableName,$whereMonth;
    private $connection_array = Array();

    /**
     * @param array $connection_array
     */
    public function setConnectionArray(array $connection_array)
    {
        $this->connection_array = $connection_array;
    }

    /**
     * @param mixed $whereMonth
     */
    public function setWhereMonth($whereMonth)
    {
        $this->whereMonth = $whereMonth;
    }
    /**
     * @param mixed $tableName
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }


    public function create($pathNameReport)
    {
        $report = new  \models\Reports();
        $report->setConnectionArray($this->connection_array);
        $report->setWait(1);
        $report->prepareReport($pathNameReport);
        $this->prepareData($report);

        $report->runCreateReport();

        return $report->getGUIDReport();
    }

    /**
     * @param $classReports \models\Reports
     */
    public function prepareData($classReports)
    {
        $conn = new \backend\Connection($this->connection_array);
        /*
         * Подготовка Шапки
         */
        $dataHead = Array();
        $dataHead = $conn->table('View_main_properties')
            ->where('id_month',$_SESSION['id_month0'])
            ->where('ORG',$_SESSION['ORG'])
            ->select()->fetch();
        $dataHead['tableName'] =  $this->tableName;
        $dataHead['whereMonth'] =  $this->whereMonth;

        $classReports->headReport($dataHead);

    }
}