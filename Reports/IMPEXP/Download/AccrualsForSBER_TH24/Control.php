<?php
namespace Reports\IMPEXP\Download\AccrualsForSBER_TH24;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);



        $this->descriptionReport = "Выгрузка данных для Сбер банка + ГИС ЖКХ с количеством месяцев задолженности";
        $this->manageTable = 'month_DAM_download_accruals_mail';


        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();


        $this->VIEW = new VIEW();
        $this->defineViewVariable();

    }

    public function run()
    {
        $H = $this->MODEL->getHeadArray();

        //$this->extensionName = $this->MODEL->getExtensionForRegion($H['DataAccrualsForSBER_id_region']);
        //$this->extensionName = "txt";

        $fileName = sprintf('%02d',date('d')).sprintf('%02d',date('m')).sprintf('%02d',date('y'));
        $fileName = "{$H['INN']}_{$H['RSCH']}_001_{$fileName}.txt";
        $this->MODEL->nameReport = $fileName ;
        $this->nameReport = $fileName;
        $this->defineModelVariable();


        $this->MODEL->updateReports_register();
        $this->nameReport = $this->id_report;
        $this->defineViewVariable();

        $data = $this->MODEL->getDataArray();
        $this->VIEW->setData($data);
        $this->VIEW->fillInFile();
    }
}