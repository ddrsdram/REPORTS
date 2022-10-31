<?php
namespace Reports\Payments\RegisterOfPayments;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);

        $this->nameReport = "Реестр платежей";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Реестр платежей сформированный согласно фильтра";
        $this->manageTable = 'list_LS_reports_payments';

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
        $report->setExcelPatternName('RegisterOfPayments.xlsx');
        $report->setH($this->MODEL->getHeadArray());
        $report->setArray("t1",$data);

        $report->run();
        //sleep(10);
        // TODO: Implement run() method.
    }
}