<?php
namespace Reports\AreDifferent\Report2;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);
        $this->nameReport = "Отчет о начислениях (в ширину)";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Отчет о начислениях (в ширину)";
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
        $ORG = $this->MODEL->getORG();
        $data_array = $this->MODEL->getDataTable($id_user,$ORG);


        $this->VIEW->setHeadSettings($this->MODEL->getHeadArray());
        $this->VIEW->setDataArray($data_array);
        $this->VIEW->setNameReport($this->id_report.$this->extensionName);
        $this->VIEW->createReport();
    }
}