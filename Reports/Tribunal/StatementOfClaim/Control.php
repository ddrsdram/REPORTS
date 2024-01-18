<?php
namespace Reports\Tribunal\StatementOfClaim;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);
        $this->nameReport = "Исковое заявление о взыскании задолженности";
        $this->extensionName = ".docx";
        $this->descriptionReport = "Исковое заявление о взыскании задолженности";
        $this->manageTable = 'list_FIO_REP';

        $this->VIEW = new VIEW();
        $this->defineViewVariable();

        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();
    }

    public function run()
    {

        $this->MODEL->updateReports_register();

        $data = $this->MODEL->getDataArray();
        $headArray = $this->MODEL->getHeadArray();


        $this->VIEW->init();

        $this->VIEW->setResultFileName($this->id_report);
        $this->VIEW->setData($data);
        $this->VIEW->setDataHead($headArray);
        $this->VIEW->setMODEL($this->MODEL);

        $this->VIEW->FillingInData();
        $this->VIEW->saveFile();


        //sleep(10);
        // TODO: Implement run() method.
    }
}