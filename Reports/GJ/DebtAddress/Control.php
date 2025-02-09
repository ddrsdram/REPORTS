<?php
namespace Reports\GJ\DebtAddress;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);
        $this->nameReport = "Список адресов для с судебным делопроизводством для задолженноти КСЗ";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Список адресов для с судебным делопроизводством для задолженноти КСЗ";
        $this->manageTable = 'list_LS_debt';

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
        $report->setExcelPatternName('DebtAddress.xlsx');
        $report->setResultFileName($this->id_report);
        //$report->setH($this->MODEL->getDataArrayHead());
        $report->setArray("t1",$this->MODEL->getDataArray());
        $report->setShowGridlines(false);
        $report->run();
        //sleep(10);
        // TODO: Implement run() method.
    }
}