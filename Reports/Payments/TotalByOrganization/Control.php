<?php
namespace Reports\Payments\TotalByOrganization;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);

        $this->nameReport = "Итоги по перечисляющим организациям";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Итоги по перечисляющим организациям";
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
        $report->setExcelPatternName('TotalByOrganization.xlsx');
        $report->setH($this->MODEL->getHeadArray());
        $report->setArray("t1",$data);

        $report->run();
        //sleep(10);
        // TODO: Implement run() method.
    }
}