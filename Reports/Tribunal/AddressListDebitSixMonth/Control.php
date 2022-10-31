<?php
namespace Reports\Tribunal\AddressListDebitSixMonth;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);
        $this->nameReport = "Список адресов с непросуженным долгом более 6 месяцев";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Список адресов с непросуженным долгом более 6 месяцев";
        $this->manageTable = 'month_reports_tribunal';

        $this->VIEW = new VIEW();
        $this->defineViewVariable();

        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();
    }

    public function run()
    {
        $this->MODEL->updateReports_register();


        $data = $this->MODEL->getDataTable($this->manageTable);

        $report = new \models\ReportOnPattern();

        $report->setExcelPatternPath(__DIR__);
        $report->setExcelPatternName('AddressListDebitSixMonth.xlsx');
        $report->setResultFileName($this->id_report);

        $report->setH($this->MODEL->getHeadArray());
        $report->setArray("t1",$data);

        $report->run();
        //sleep(10);
        // TODO: Implement run() method.
    }
}