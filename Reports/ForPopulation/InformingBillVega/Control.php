<?php
namespace Reports\ForPopulation\InformingBillVega;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);
        $this->nameReport = "Извешение";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Извещение на половину страницы";
        $this->manageTable = 'list_LS_Bill';

        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();


        $this->VIEW = new VIEW();
        $this->defineViewVariable();

    }

    public function run()
    {
        $headArray = $this->MODEL->getHeadArray();
        $this->MODEL->nameReport .= $headArray['addNameFile'];

        $this->MODEL->updateReports_register();
        $dataArray = $this->MODEL->getDataArray();

        $this->VIEW->setDataHead($headArray);
        $this->VIEW->setDataAllLS($dataArray);
        $this->VIEW->setExcelPatternPath(__DIR__);
        $this->VIEW->setResultFileName($this->id_report);
        $this->VIEW->setColumnsPattern(11);
        $this->VIEW->setRowsPattern(37);
        $this->VIEW->run();
    }

}