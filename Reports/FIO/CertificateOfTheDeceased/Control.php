<?php
namespace Reports\FIO\CertificateOfTheDeceased;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);

        $this->nameReport = "Справка на умершего";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Справка на умершего";
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
        $report->setExcelPatternName('CertificateOfTheDeceased.xlsx');
        $report->setResultFileName($this->id_report);
        $report->setH($this->MODEL->getDataArrayHead());
        $report->setArray("t1",$this->MODEL->getDataArray());

        $report->run();
        //sleep(10);
        // TODO: Implement run() method.
    }
}