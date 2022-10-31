<?php
namespace Reports\LS\HistoryDevceValue;

class Control extends \Reports\reportControl
{
    function __construct($id_report)
    {
        parent::__construct($id_report);
        $this->nameReport = "История показаний приборов учета";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "История показаний приборов учета";
        $this->manageTable = 'list_LS_reports_device';

        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();
    }

    public function run()
    {
        $this->MODEL->updateReports_register();
        $id_user = $this->MODEL->getUser();
        $data1 = $this->MODEL->getDataTable($id_user);

        $report = new \models\ReportOnPattern();

        $report->setExcelPatternPath(__DIR__);
        $report->setExcelPatternName('HistoryDevceValue.xlsx');
        $report->setResultFileName($this->id_report);
        $report->setH($this->MODEL->getHeadArray());
        $report->setArray("t1",$data1);
        $report->setArray("t2",Array());
        $report->run();
    }
}