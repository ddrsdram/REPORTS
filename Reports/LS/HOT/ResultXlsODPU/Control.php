<?php
namespace Reports\LS\HOT\ResultXlsODPU;



class Control extends \Reports\reportControl
{
    function __construct($id_report)
    {
        parent::__construct($id_report);
        $this->nameReport = "Файл данных ОДПУ c исправлениями";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Файл данных ОДПУ c исправлениями";
        $this->manageTable = '';

        $this->VIEW = new VIEW();
        $this->defineViewVariable();

        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();
    }

    public function run()
    {
        $this->extensionName = "";
        $this->MODEL->updateReports_register();
        $this->MODEL->saveFile($this->id_report);
        $this->VIEW->setMODEL($this->MODEL);
        $this->VIEW->openFile();
        $this->VIEW->addData();
        $this->VIEW->save();
    }
}