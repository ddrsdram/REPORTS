<?php
namespace Reports\Tribunal\HelpCalcPenaltySite;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);
        $this->nameReport = "Помощь для провери расчета пени на сайте https://dogovor-urist.ru/calculator/peni_155zhk/";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Помощь для провери расчета пени на сайте https://dogovor-urist.ru/calculator/peni_155zhk/";
        $this->manageTable = 'month_reports_tribunal';

        $this->VIEW = new VIEW();
        $this->defineViewVariable();

        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();
    }

    public function run()
    {
        $this->MODEL->updateReports_register();
        $id_user = $this->MODEL->getUser();

        $headData = $this->MODEL->getHeadArray();
        $this->MODEL->setAllRuling($headData['AllRuling']);


        $report = new \models\ReportOnPattern();

        $report->setExcelPatternPath(__DIR__);
        $report->setExcelPatternName('HelpCalcPenaltySite.xlsx');
        $report->setResultFileName($this->id_report);

        $report->setH($this->MODEL->getHeadArray());
        $report->setArray("t1",$this->MODEL->getSumAccrual());
        $report->setArray("t2",$this->MODEL->getSumPayment());

        $report->run();
        //sleep(10);
        // TODO: Implement run() method.
    }
}