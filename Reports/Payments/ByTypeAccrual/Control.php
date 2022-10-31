<?php
namespace Reports\Payments\ByTypeAccrual;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);
        $this->nameReport = "Оплата с детализацией по видам начислений";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Оплата с детализацией по видам начислений";
        $this->manageTable = 'list_LS_reports_payments';

        $this->VIEW = new VIEW();
        $this->defineViewVariable();

        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();
    }

    public function run()
    {
        $this->MODEL->updateReports_register();
        $data_array = $this->MODEL->getDataTable();


        $this->VIEW->setHeadSettings($this->MODEL->getHeadArray());
        $this->VIEW->setDataArray($data_array);
        $this->VIEW->setNameReport($this->id_report.$this->extensionName);
        $this->VIEW->createReport();

    }
}