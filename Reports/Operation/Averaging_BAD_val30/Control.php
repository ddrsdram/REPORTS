<?php
namespace Reports\Operation\Averaging_BAD_val30;

use models\ErrorLog;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);
        $this->nameReport = "Не усреднившиеся показания из за объема более 30кубов";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Не усреднившиеся показания из за объема более 30кубов";
        $this->manageTable = 'list_LS_reports';


        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();
    }

    public function run()
    {
        $this->MODEL->updateReports_register();
        $data = $this->MODEL->getData();
        $path = $_SERVER['DOCUMENT_ROOT']."/ImpExp/";
        $copyToExcel = new  \models\CopyToExcel();
        $copyToExcel->setFileName($this->id_report.$this->extensionName)
            ->setPath($path)
            ->setDataArray($data)
            ->save();
        //sleep(10);
        // TODO: Implement run() method.
        //unset($copyToExcel);
    }
}