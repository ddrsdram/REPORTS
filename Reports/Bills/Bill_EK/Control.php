<?php
namespace Reports\Bills\Bill_EK;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);

        $this->nameReport = "Извещение";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Формирование извещения для ООО \"Энергокомпания\"";

        $this->VIEW = new VIEW();
        $this->defineViewVariable();

        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();
    }

    public function run()
    {
        $this->MODEL->updateReports_register();
        $report = new \models\ReportOnPattern();

        $report->setExcelPatternPath(__DIR__);
        $report->setResultFileName($this->id_report);
        $report->setH($this->MODEL->getHeadArray());
        $report->setArray("t1",$this->MODEL->getDataArray());

        $report->run();
        //sleep(10);
        // TODO: Implement run() method.
    }
}