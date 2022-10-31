<?php
namespace Reports\Tribunal\Report1;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);
        $this->nameReport = "Перечень исполнительных документов";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Перечень исполнительных документов";
        $this->manageTable = 'month_reports_tribunal';

        $this->VIEW = new VIEW();
        $this->defineViewVariable();

        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();
    }

    public function run()
    {
        $this->MODEL->updateReports_register();
        $id_user = $this->MODEL->getUser();

        $headData = $this->MODEL->getHeadArray();
        $this->MODEL->setAllRuling($headData['AllRuling']);

        $data = $this->MODEL->getDataTable($id_user);

        $report = new \models\ReportOnPattern();

        $report->setExcelPatternPath(__DIR__);
        $report->setExcelPatternName('TribunalReport1.xlsx');
        $report->setResultFileName($this->id_report);

        $report->setH($this->MODEL->getHeadArray());
        $report->setArray("t1",$data);

        $report->run();
        //sleep(10);
        // TODO: Implement run() method.
    }
}