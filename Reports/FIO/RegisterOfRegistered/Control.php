<?php
namespace Reports\FIO\RegisterOfRegistered;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);
        $this->nameReport = "Реестр зарегестрированных";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Реестр зарегестрированных";
        $this->manageTable = 'list_LS_reports';

        $this->VIEW = new VIEW();
        $this->defineViewVariable();

        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();
    }

    public function run()
    {
        $this->MODEL->updateReports_register();

        $id_user = $this->MODEL->getUser();

        $data = $this->MODEL->getDataArray();
        $headArray = $this->MODEL->getHeadArray();


        $this->VIEW->init();

        $this->VIEW->setResultFileName($this->id_report);
        $this->VIEW->setData($data);
        $this->VIEW->setDataHead($headArray);

        $this->VIEW->FillingInData();
        $this->VIEW->saveFile();

    }
}