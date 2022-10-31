<?php
namespace Reports\ForPopulation\advert;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);
        $this->nameReport = "Извещения по подъезам";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Извещения по подъезам";
        $this->manageTable = 'list_LS_reports';

        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();



    }

    public function run()
    {
        $this->MODEL->updateReports_register();

        $report = new \models\ReportOnPattern();

        $data = $this->MODEL->getDataTable();
        $report->setArray("t1",$data);
        $report->setArray("t2",Array());

        $report->setExcelPatternPath(__DIR__);
        $report->setExcelPatternName('advert.xlsx');
        $report->setResultFileName($this->id_report);
       // $report->setH($this->MODEL->getHeadArray());

        $report->run();
    }

}