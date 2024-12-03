<?php
namespace Reports\Tribunal\StatementTribunalOrder_0people;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);
        $this->nameReport = "Заявление о выдачи судебного приказа (0-зарегистрированных)";
        $this->extensionName = ".docx";
        $this->descriptionReport = "Заявление о выдачи судебного приказа (0-зарегистрированных)";
        $this->manageTable = 'list_FIO_REP';

        $this->VIEW = new VIEW();
        $this->defineViewVariable();

        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();
    }

    public function run()
    {

        $this->MODEL->updateReports_register();
        $id_user = $this->MODEL->getUser();

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