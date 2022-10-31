<?php
namespace Reports\IMPEXP\Download\AccrualsForMail_OLD;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);

        $this->nameReport = "post1";
        $this->extensionName = ".dbf";
        $this->descriptionReport = "Выгрузка данных на почту";
        $this->manageTable = 'month_DAM_download_accruals_mail';


        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();

        $this->VIEW = new VIEW();
        $this->defineViewVariable();

    }

    public function run()
    {

        $this->MODEL->updateReports_register();
        $this->nameReport = $this->id_report;
        $this->defineViewVariable();

        $data = $this->MODEL->getDataArray();
        $this->VIEW->setData($data);
        $this->VIEW->fillInFile();
    }
}