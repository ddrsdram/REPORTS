<?php
namespace Reports\PassportOffice\InOut;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);
        $this->nameReport = "Прописка-Выписка";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Прописка-Выписка";
        $this->manageTable = 'list_LS_PassportOffice';

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
        $report->setExcelPatternName('InOut.xlsx');
        $report->setResultFileName($this->id_report);

        $report->setH($this->MODEL->getHeadArray());
        $report->setArray("t1",Array());

        \models\ErrorLog::saveError("start", typeSaveMode: 'w+');
        $data = $this->MODEL->getDataTable();
        //\models\ErrorLog::saveError($data);
        $report->setArray("t2",$data);

        $report->run();
        //sleep(10);
        // TODO: Implement run() method.
    }
}