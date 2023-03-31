<?php
namespace Reports\Recalculation\FullRegisterByLS;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);

        $this->nameReport = "Полный реестр справок(перерасчетов) детализированый по ЛС";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Полный реестр справок(перерасчетов) детализированый по ЛС";
        $this->manageTable = 'list_LS_reports_payments';

        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();
    }

    public function run()
    {
        $this->MODEL->updateReports_register();

        $report = new \models\ReportOnPattern();
        $report->setExcelPatternPath(__DIR__);
        $report->setResultFileName($this->id_report);
        $report->setExcelPatternName('RecalculationFullRegisterByLS.xlsx');


        $headArray = $this->MODEL->getHeadArray();
        $data = $this->MODEL->getDataTable($headArray);

        $report->setH($headArray);
        $report->setArray("t1",$data);

        $report->run();
        //sleep(10);
        // TODO: Implement run() method.
    }
}