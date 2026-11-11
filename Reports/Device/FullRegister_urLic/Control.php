<?php
namespace Reports\Device\FullRegister_urLic;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);
        $this->nameReport = "Полный реестр приборов учета Юридические лица";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Полный реестр приборов учета Юридические лица";
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
        $report->setExcelPatternName('FullRegister_urLic.xlsx');

        $report->setResultFileName($this->id_report);
        $h= $this->MODEL->getHeadArray();
        \models\ErrorLog::saveError($h,typeSaveMode: "w+");

        $report->setH($h);
        $report->setArray("t1",$data_array);

        $report->run();

    }
}