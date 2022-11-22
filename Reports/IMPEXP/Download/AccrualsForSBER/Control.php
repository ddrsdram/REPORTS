<?php
namespace Reports\IMPEXP\Download\AccrualsForSBER;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);

        $this->nameReport = "file.txt";

        $this->descriptionReport = "Выгрузка данных для Сбер банка";
        $this->manageTable = 'month_DAM_download_accruals_mail';


        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();


        $this->VIEW = new VIEW();
        $this->defineViewVariable();

    }

    public function run()
    {
        $H = $this->MODEL->getHeadArray();
        $this->extensionName = $this->MODEL->getExtensionForRegion($H['DataAccrualsForSBER_id_region']);
        $this->defineModelVariable();


        $fileName = "tc".sprintf('%02d',date('d')).sprintf('%02d',date('m'))."0.";
        $this->MODEL->nameReport = $fileName;

        $this->MODEL->updateReports_register();
        $this->nameReport = $this->id_report;
        $this->defineViewVariable();

        $data = $this->MODEL->getDataArray();
        $this->VIEW->setData($data);
        $this->VIEW->fillInFile();
    }
}