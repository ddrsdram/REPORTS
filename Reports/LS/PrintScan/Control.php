<?php
namespace Reports\LS\PrintScan;

class Control extends \Reports\reportControl
{
    function __construct($id_report)
    {
        parent::__construct($id_report);
        $this->nameReport = "Сохранить скан";
        $this->extensionName = ".pdf";
        $this->descriptionReport = "Сохранить скан";
        $this->manageTable = 'list_LS_reports_device';

        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();
    }

    public function run()
    {
        $this->MODEL->updateReports_register();
        $this->MODEL->saveFile($this->id_report);
    }
}