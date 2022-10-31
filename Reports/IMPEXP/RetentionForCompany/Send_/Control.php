<?php
namespace Reports\IMPEXP\RetentionForCompany\Send_;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);

        $this->nameReport = "Форма для отправки в организацию";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Форма для отправки в организацию";
        $this->manageTable = 'list_LS_decoding_reports';


        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();
    }

    public function run()
    {
        $this->MODEL->updateReports_register();

        $report = new \models\ReportOnPattern();

        $report->setExcelPatternPath(__DIR__);
        $report->setExcelPatternName('Send_.xlsx');
        $report->setResultFileName($this->id_report);
        $report->setH($this->MODEL->getHeadArray());
        $data1 = $this->MODEL->getBodyByQuery();
        $report->setArray("t1",$data1);

        $report->run();
        //sleep(10);
        // TODO: Implement run() method.
    }
}