<?php
namespace Reports\RSO\Prokopevsk\HotWater_and_HOT;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);
        $this->nameReport = "Горячее водоснабжение И отопление (для Прокопьевского муниципального округа)";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Горячее водоснабжение И отопление (для Прокопьевского муниципального округа)";
        $this->manageTable = 'list_LS_decoding_reports';

        $this->VIEW = new VIEW();
        $this->defineViewVariable();

        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();
    }

    public function run()
    {
        $this->MODEL->updateReports_register();

        $this->MODEL->setProperties();

        $data1 = $this->MODEL->getDataTable_HotWater();
        $data2 = $this->MODEL->getDataTable_Hot();

        $report = new \models\ReportOnPattern();

        $report->setExcelPatternPath(__DIR__);
        $report->setExcelPatternName('HotWater_and_HOT.xlsx');
        $report->setResultFileName($this->id_report);

        $report->setH($this->MODEL->getHeadArray());
        $report->setArray("t2",$data1);
        $report->setArray("t1",$data2);

        $report->run();
        //sleep(10);
        // TODO: Implement run() method.
    }
}