<?php
namespace Reports\RSO\Prokopevsk\Hot_water_pSector;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);
        $this->nameReport = "Горячее водоснабжение форма для частного сектора (для Прокопьевского муниципального округа)";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Горячее водоснабжение форма для частного сектора (для Прокопьевского муниципального округа)";
        $this->manageTable = 'list_LS_decoding_reports';

        $this->VIEW = new VIEW();
        $this->defineViewVariable();

        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();
    }

    public function run()
    {
        $this->MODEL->updateReports_register();
        $id_user = $this->MODEL->getUser();
        $data = $this->MODEL->getDataTable($id_user);

        $report = new \models\ReportOnPattern();

        $report->setExcelPatternPath(__DIR__);
        $report->setExcelPatternName('Hot_water_pSector.xlsx');
        $report->setResultFileName($this->id_report);

        $report->setH($this->MODEL->getHeadArray());
        $report->setArray("t1",$data);

        $report->run();
        //sleep(10);
        // TODO: Implement run() method.
    }
}