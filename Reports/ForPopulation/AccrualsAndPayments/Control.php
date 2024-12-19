<?php
namespace Reports\ForPopulation\AccrualsAndPayments;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);
        $this->nameReport = "Начислено оплачено по году";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Начислено оплачено по году";
        $this->manageTable = 'list_LS_reports';

        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();
    }

    public function run()
    {
        $this->MODEL->updateReports_register();

        $data1 = $this->MODEL->getDataArray();

        $report = new \models\ReportOnPattern();

        $report->setExcelPatternPath(__DIR__);
        $report->setExcelPatternName('AccrualsAndPayments.xlsx');
        $report->setResultFileName($this->id_report);

        $headSettings = $this->MODEL->getHeadArray();

        $report->setH($headSettings);
        $report->setArray("t1",$data1);
        $report->run();
    }

}