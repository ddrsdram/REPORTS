<?php
namespace Reports\Payments\WithDetailsBySupplyingOrganizations;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);

        $this->nameReport = "Сформировать с детализацией по снабжающим организациям (итоговая)";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Сформировать с детализацией по снабжающим организациям (итоговая)";
        $this->manageTable = 'list_LS_reports_payments';

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
        $report->setExcelPatternName('pattern.xlsx');
        $report->setH($this->MODEL->getHeadArray());
        $report->setArray("t1",$data);

        $report->run();
        //sleep(10);
        // TODO: Implement run() method.
    }
}