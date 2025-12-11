<?php
namespace Reports\Debt\Report1;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);
        $this->nameReport = "Список должников по адресно";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Список должников по адресно";
        $this->manageTable = 'list_LS_debt';

        $this->VIEW = new VIEW();
        $this->defineViewVariable();

        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();
    }

    public function run()
    {
        $this->MODEL->updateReports_register();
        $id_user = $this->MODEL->getUser();

        $data = $this->MODEL->getDataTable($id_user);

        $report = new \models\ReportOnPattern();

        $report->setExcelPatternPath(__DIR__);
        $report->setResultFileName($this->id_report);

        $report->setH($this->MODEL->getHeadArray());
        $report->setArray("t1",$data);

        $report->printArea_enabled();
        $report->run();
        //sleep(10);
        // TODO: Implement run() method.
    }
}