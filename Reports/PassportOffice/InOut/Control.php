<?php
namespace Reports\PassportOffice\InOut;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);
        $this->nameReport = "Прописка-Выписка";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Прописка-Выписка";
        $this->manageTable = 'list_LS_PassportOffice';

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
        $report->setShowGridlines(false);
        $report->setExcelPatternName('InOut.xlsx');
        $report->setResultFileName($this->id_report);
        $head = $this->MODEL->getHeadArray();
        $report->setH($head);
        $report->setArray("t1",Array());

        $data = $this->MODEL->getDataTable();
        $report->setArray("t2",$data);

        $report->run();
    }
}