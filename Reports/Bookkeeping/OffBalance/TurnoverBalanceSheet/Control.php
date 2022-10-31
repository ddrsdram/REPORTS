<?php
namespace Reports\Bookkeeping\OffBalance\TurnoverBalanceSheet;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);


        $this->nameReport = "Оборотносальдовая ведомость";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Оборотносальдовая ведомость";
        $this->manageTable = 'off_balance_monthForReports';


        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();


    }

    public function run()
    {
        $this->MODEL->updateReports_register();

        $data1 = $this->MODEL->getDataTable();

        $report = new \models\ReportOnPattern();

        $report->setExcelPatternPath(__DIR__);
        $report->setExcelPatternName('TurnoverBalanceSheet.xlsx');
        $report->setResultFileName($this->id_report);
        $report->setH($this->MODEL->getHeadArray());
        $report->setArray("t1",$data1);

        $report->run();
    }
}