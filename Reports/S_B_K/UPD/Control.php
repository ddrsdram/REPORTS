<?php
namespace Reports\S_B_K\UPD;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);

        $this->nameReport = "Универсальный передаточный документ";
        $this->extensionName = ".pdf";
        $this->descriptionReport = "Универсальный передаточный документ";
        $this->manageTable = 'None';

        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();
    }

    public function run()
    {

        $this->MODEL->updateReports_register();

        $data = $this->MODEL->getDataTable();

        $report = new \models\ReportOnPattern();

        $report->setExcelPatternPath(__DIR__);
        $report->setExcelPatternName('UPD.xlsx');
        $report->setResultFileName($this->id_report);
        $report->setH($data);

        $report->saveToPdf();
        $report->copyPatternToDestination();
        $report->run();
        //sleep(10);
        // TODO: Implement run() method.
    }
}