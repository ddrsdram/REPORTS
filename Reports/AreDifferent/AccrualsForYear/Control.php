<?php
namespace Reports\AreDifferent\AccrualsForYear;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);
        $this->nameReport = "Начислениея по году";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Начислениея по году";
        $this->manageTable = 'list_LS_reports';

        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();


        $this->VIEW = new VIEW();
        $this->defineViewVariable();

    }

    public function run()
    {
        $dataArray = $this->MODEL->getDataArray();
        $dataTypeAccrual = $this->MODEL->getTypeAccrual();

        $this->VIEW->setHeadSettings($this->MODEL->getHeadArray());

        $this->VIEW->setData($dataArray);
        $this->VIEW->setTypeAccrual($dataTypeAccrual);

        $this->MODEL->updateReports_register();

        $this->VIEW->setExcelPatternPath(__DIR__);
        $this->VIEW->setResultFileName($this->id_report);

        $this->VIEW->run();
    }

}