<?php
namespace Reports\Device\Register_averaging;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);
        $this->nameReport = "Реестр приборов по которым произведено усредение";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Реестр приборов по которым произведено усредение";
        $this->manageTable = 'list_device';

        $this->VIEW = new VIEW();
        $this->defineViewVariable();

        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();
    }

    public function run()
    {
        $this->MODEL->updateReports_register();
        $id_user = $this->MODEL->getUser();
        $data_array = $this->MODEL->getDataTable($id_user);


        $report = new \models\ReportOnPattern();

        $report->setExcelPatternPath(__DIR__);
        $report->setExcelPatternName('Register_averaging.xlsx');

        $report->setResultFileName($this->id_report);

        $report->setH($this->MODEL->getHeadArray());
        $report->setArray("t1",$data_array);

        $report->run();

    }
}