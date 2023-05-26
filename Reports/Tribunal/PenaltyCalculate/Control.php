<?php
namespace Reports\Tribunal\PenaltyCalculate;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);
        $this->nameReport = "Расчет пени";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Расчет пени";
        $this->manageTable = 'month_reports_tribunal';

        $this->VIEW = new VIEW();
        $this->defineViewVariable();

        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();
    }

    public function run()
    {
        $this->MODEL->updateReports_register();

        $data = $this->MODEL->getDataTable();

        $report = new \models\ReportOnPattern();

        $report->setExcelPatternPath(__DIR__);
        $report->setExcelPatternName('PenaltyCalculate.xlsx');
        $report->setResultFileName($this->id_report);


        $report->setH($this->MODEL->getHeadArray());
        $report->setArray("t2",$data);
        $report->setArray("t1",Array());
        $report->setShowGridlines(false);
        $report->run();
        //sleep(10);
        // TODO: Implement run() method.
    }
}