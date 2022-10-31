<?php
namespace Reports\backUpForORG;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);
        $this->nameReport = "!_backUpForORG_Accrual";
        $this->extensionName = ".txt";
        $this->descriptionReport = "!_backUpForORG_Accrual";
        $this->manageTable = 'list_LS_FullUpload';

        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();


        $this->VIEW = new VIEW();
        $this->defineViewVariable();

    }

    public function run()
    {
        $this->MODEL->updateReports_register();
        $data = $this->MODEL->getDataTable();

        $this->VIEW->setData($data);
        $this->VIEW->fillInFile();

    }
}