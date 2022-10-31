<?php
namespace Reports\IMPEXP\RetentionForCompany\Print_;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);

        $this->nameReport = "Форма для печати";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Форма для печати";


        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();
    }

    public function run()
    {
        $this->MODEL->updateReports_register();

        $report = new \models\ReportOnPattern();

        $report->setExcelPatternPath(__DIR__);
        $report->setExcelPatternName('Print_.xlsx');
        $report->setResultFileName($this->id_report);
        $report->setH($this->MODEL->getHeadArray());
        $data1 = $this->MODEL->getBodyByQuery();
        $report->setArray("t1",$data1);

        $report->run();
        //sleep(10);
        // TODO: Implement run() method.
    }
}