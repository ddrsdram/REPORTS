<?php
namespace Reports\FIO\Certificate;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);

        $this->nameReport = "Справка с паспортного стола";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Справка с паспортного стола";
        $this->manageTable = 'list_FIO_REP';

        $this->VIEW = new VIEW();
        $this->defineViewVariable();

        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();
    }

    public function run()
    {
        $this->MODEL->updateReports_register();
        $report = new \models\ReportOnPattern();

        $report->setExcelPatternPath(__DIR__);
        $report->setExcelPatternName('Certificate.xlsx');
        $report->setResultFileName($this->id_report);
        $report->setH($this->MODEL->getDataArrayHead());
        $report->setArray("t1",$this->MODEL->getDataArray());
        $report->setShowGridlines(false);
        $report->run();
        //sleep(10);
        // TODO: Implement run() method.
    }
}