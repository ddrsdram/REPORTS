<?php
namespace Reports\FIO\Certificate_NoRegistration;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);

        $this->nameReport = "Справка об отсутствии прописанных";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Справка об отсутствии прописанных";
        $this->manageTable = 'list_LS_Bill';

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
        $report->setExcelPatternName('Certificate_NoRegistration.xlsx');
        $report->setResultFileName($this->id_report);
        \models\ErrorLog::saveError($this->MODEL->getHeadArray());
        $report->setH($this->MODEL->getHeadArray());
        $report->setShowGridlines(false);
        $report->run();
        //sleep(10);
        // TODO: Implement run() method.
    }
}