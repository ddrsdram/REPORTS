<?php
namespace Reports\ForPopulation\ReferenceForSubsidy;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);
        $this->nameReport = "Справка для субсидии";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Справка для субсидии";
        $this->manageTable = 'list_LS_Bill';

        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();


        $this->VIEW = new VIEW();
        $this->defineViewVariable();

    }

    public function run()
    {
        $this->MODEL->updateReports_register();

        $report = new \models\ReportOnPattern();

        $data = $this->MODEL->getDataTable();
        $report->setArray("t1",$data);

        $data2 = $this->MODEL->getDataTotal();
        $report->setArray("t2",$data2);
/*



 */
        $report->setExcelPatternPath(__DIR__);
        $report->setExcelPatternName('pattern_.xlsx');
        $report->setResultFileName($this->id_report);
        $report->setH($this->MODEL->getHeadArray());

        $report->run();
    }

}