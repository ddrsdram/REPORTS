<?php
namespace Reports\IMPEXP\Download\DeviceValue;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);

        $this->nameReport = "Выгрузка обработанных данных по проборам учета";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Выгрузка обработанных данных по проборам учета";
        $this->manageTable = 'TABLE-NONE';


        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();



    }

    public function run()
    {
        $this->MODEL->updateReports_register();
        $data = $this->MODEL->getDataArray();

        $report = new \models\ReportOnPattern();

        $report->setExcelPatternPath(__DIR__);
        $report->setExcelPatternName('DeviceValue.xlsx');
        $report->setResultFileName($this->id_report);
        $report->setArray("t1", $data);

        $report->run();
        //sleep(10);
        // TODO: Implement run() method.

    }
}