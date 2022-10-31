<?php
namespace Reports\IMPEXP\Download\AccrualsForMail;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);

        $this->nameReport = "file.txt";
        $this->extensionName = ".txt";
        $this->descriptionReport = "Выгрузка данных для Почтабанка";
        $this->manageTable = 'month_DAM_download_accruals_mail';


        $this->MODEL = new MODEL($this->id_report);

        $this->defineModelVariable();


        $this->VIEW = new VIEW();
        $this->defineViewVariable();

    }

    public function run()
    {


        $fileName = "4202044463_40702810961880000167_001_".sprintf('%02d',date('d')).sprintf('%02d',date('m'))."_";
        $this->MODEL->nameReport = $fileName;

        $this->MODEL->updateReports_register();
        $this->nameReport = $this->id_report;
        $this->defineViewVariable();

        $data = $this->MODEL->getDataArray();
        $this->VIEW->setData($data);
        $this->VIEW->fillInFile();
    }
}