<?php
namespace Reports\AreDifferent\accrualsFor_typeAccrual_ByHouse;

class Control extends \Reports\reportControl
{
    function __construct($id_report)
    {
        parent::__construct($id_report);
        $this->nameReport = 'Начисления по виду начисления "Текущий ремонт" с детализацией по домам';
        $this->extensionName = ".xlsx";
        $this->descriptionReport = 'Начисления по виду начисления "Текущий ремонт" с детализацией по домам';
        $this->manageTable = 'list_LS_decoding_reports';

        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();
    }

    public function run()
    {
        $this->MODEL->updateReports_register();

        $data1 = $this->MODEL->getDataTable();

        $report = new \models\ReportOnPattern();

        $report->setExcelPatternPath(__DIR__);
        $report->setExcelPatternName('accrualsFor_typeAccrual_ByHouse.xlsx');
        $report->setResultFileName($this->id_report);
        $report->setH($this->MODEL->getHeadArray());
        $report->setArray("t1",$data1);

        $report->run();
    }
}