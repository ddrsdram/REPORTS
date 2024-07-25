<?php
namespace Reports\IMPEXP\Download\ExportDevice_platosphere;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);

        $this->nameReport = "Приборы учета для платосферы.txt";

        $this->descriptionReport = "Выгрузка данных о приборах учата в платосферу";
        $this->manageTable = 'month_DAM_download_accruals_mail';


        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();


        $this->VIEW = new VIEW();
        $this->defineViewVariable();

    }

    public function run()
    {
        $this->defineModelVariable();


//        $fileName = "tc".sprintf('%02d',date('d')).sprintf('%02d',date('m'))."0.";
//        $this->MODEL->nameReport = $fileName;

        $this->MODEL->updateReports_register();
        $this->nameReport = $this->id_report;
        $this->defineViewVariable();

        $data = $this->MODEL->getDataArray();
        $this->VIEW->setData($data);
        $this->VIEW->fillInFile();
    }
}