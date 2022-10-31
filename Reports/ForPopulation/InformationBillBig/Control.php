<?php
namespace Reports\ForPopulation\InformationBillBig;

class Control extends \Reports\reportControl
{


    function __construct($id_report)
    {
        parent::__construct($id_report);

        $this->nameReport = "Извещение";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Извещение на всю страницу";
        $this->manageTable = 'list_LS_Bill';

        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();
    }

    public function run()
    {
        $headArray = $this->MODEL->getHeadArray();
        $this->MODEL->nameReport .= $headArray['addNameFile'];

        $this->MODEL->updateReports_register();
        $data = $this->MODEL->getDataArray();
        $report = new \models\ReportOnPattern();

        $report->setExcelPatternPath(__DIR__);
        $report->setExcelPatternName('InformationBillBig.xlsx');
        $report->setResultFileName($this->id_report);
        $report->setH($headArray);
        $report->setArray("t1",$data);
        $report->setArray("t2",Array());
        $report->setArray("t3",Array());
        $report->setArray("t4",Array());
        $report->setArray("t5",Array());

        $report->run();
        //sleep(10);
        // TODO: Implement run() method.
    }
}