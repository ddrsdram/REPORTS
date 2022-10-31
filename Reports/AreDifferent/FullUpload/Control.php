<?php
namespace Reports\AreDifferent\FullUpload;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);
        $this->nameReport = "Полная выгрузка";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Полная выгрузка";
        $this->manageTable = 'list_LS_FullUpload';

        $this->VIEW = new VIEW();
        $this->defineViewVariable();

        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();
    }

    public function run()
    {
        $this->MODEL->updateReports_register();
        $id_user = $this->MODEL->getUser();

        $data = $this->MODEL->getDataTable($id_user);
        $path = $_SERVER['DOCUMENT_ROOT']."/ImpExp/";
        $copyToExcel = new  \models\CopyToExcel();
        $copyToExcel->setFileName($this->id_report.$this->extensionName)
            ->setPath($path)
            ->setDataClass($data)
            ->save();
        //sleep(10);
        // TODO: Implement run() method.
        unset($copyToExcel);
    }
}