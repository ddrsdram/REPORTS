<?php
namespace Reports\AreDifferent\decryption_hotWater;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);

        $this->nameReport = "Расшифровка начислений по горячекй воде";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Расшифровка начислений по горячекй воде";
        $this->manageTable = 'list_LS_decoding_reports';

        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();
    }

    public function run()
    {
        $this->MODEL->updateReports_register();
        $id_user = $this->MODEL->getUser();
        $data1 = $this->MODEL->getDataTable($id_user,7,"1");
        $data2 = $this->MODEL->getDataTable($id_user,7,"0");

        $report = new \models\ReportOnPattern();

        $report->setExcelPatternPath(__DIR__);
        $report->setExcelPatternName('decryption_hotWater.xlsx');
        $report->setResultFileName($this->id_report);
        $report->setH($this->MODEL->getHeadArray());
        $report->setArray("t1",$data1);
        $report->setArray("t2",$data2);

        $report->run();
        //sleep(10);
        // TODO: Implement run() method.
    }
}