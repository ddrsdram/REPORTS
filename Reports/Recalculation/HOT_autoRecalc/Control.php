<?php
namespace Reports\Recalculation\HOT_autoRecalc;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);

        $this->nameReport = "Отчет для проверки перерасчета отопления";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Отчет для проверки перерасчета отопления";
        $this->manageTable = '';

        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();
    }

    public function run()
    {
        $this->MODEL->updateReports_register();

        $report = new \models\ReportOnPattern();
        $report->setExcelPatternPath(__DIR__);
        $report->setResultFileName($this->id_report);
        $report->setExcelPatternName('HOT_autoRecalc.xlsx');


        $headArray = $this->MODEL->getHeadArray();
        $data = $this->MODEL->getDataTable($headArray);

        $report->setH($headArray);
        $report->setArray("t1",$data);

        $report->run();
        //sleep(10);
        // TODO: Implement run() method.
    }
}