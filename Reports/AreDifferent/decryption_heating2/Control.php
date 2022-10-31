<?php
namespace Reports\AreDifferent\decryption_heating2;

class Control extends \Reports\reportControl
{
    function __construct($id_report)
    {
        parent::__construct($id_report);

        $this->nameReport = "Расшифровка начислений по отоплению";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Расшифровка начислений по отоплению (Приложение № 7 к Соглашению № 40-юр с 25.12.2020 г.)";
        $this->manageTable = 'list_LS_decoding_reports';

        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();


        $this->VIEW = new VIEW();
        $this->defineViewVariable();
    }

    public function run()
    {
        $headArray = $this->MODEL->getHeadArray();
        $this->VIEW->setDataHead($headArray);

        $this->MODEL->updateReports_register();
        $dataArray = $this->MODEL->getDataTable();

        $this->VIEW->setDataArray($dataArray);
        $this->VIEW->setExcelPatternPath(__DIR__);
        $this->VIEW->setResultFileName($this->id_report);
        $this->VIEW->setColumnsPattern(46);
        $this->VIEW->run();

    }
}