<?php
namespace Reports\IMPEXP\Upload\Payments\BadSumm;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);

        $this->nameReport = "Ошибочные суммы при загрузке платежей";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Ошибочные суммы при загрузке платежей";
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
        $report->setExcelPatternName('PaymentBadSumm.xlsx');
        $report->setResultFileName($this->id_report);
        $report->setArray("t1", $data);

        $report->run();
        //sleep(10);
        // TODO: Implement run() method.

    }
}