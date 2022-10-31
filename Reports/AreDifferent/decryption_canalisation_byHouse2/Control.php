<?php
namespace Reports\AreDifferent\decryption_canalisation_byHouse2;

class Control extends \Reports\reportControl
{


    function __construct($id_report)
    {
        parent::__construct($id_report);

        $this->nameReport = "Расшифровка начислений по водоотведению";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Расшифровка начислений по водоотведению  (Приложение № 7 к Соглашению № 40-юр  с 25.12.2020 г.)";
        $this->manageTable = 'list_LS_decoding_reports';

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
        $report->setExcelPatternName('decryption_canalisation_byHouse2.xlsx');
        $report->setResultFileName($this->id_report);
        $report->setH($this->MODEL->getHeadArray());
        $report->setArray("t1",$data);

        $report->run();
        //sleep(10);
        // TODO: Implement run() method.

    }
}