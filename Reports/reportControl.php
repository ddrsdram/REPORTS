<?php
namespace Reports;

abstract class reportControl
{
    public $id_report;
    public $nameReport;
    public $extensionName;
    public $descriptionReport;
    public $manageTable;

    public $MODEL;
    public $VIEW;

    function __construct($id_report)
    {
        $this->id_report = $id_report;
    }

    function __destruct()
    {
        /*
        // TODO: Implement __destruct() method.

        $conn = new \backend\Connection();
        $conn->table($this->manageTable)
            ->where('id_user',$this->MODEL->getUser())
            ->delete();
*/
    }

    abstract protected function run();

    public function getNameReport()
    {
        return $this->nameReport;
    }

    public function getFileNameReport()
    {
        return $this->id_report.$this->extensionName;
    }


    public function getDescriptionReport()
    {
        return $this->descriptionReport;
    }

    /**
     * @return mixed
     */
    public function getManageTable()
    {
        return $this->manageTable;
    }

    public function defineViewVariable()
    {
        if (is_object ($this->VIEW)){
            $this->VIEW->id_report = $this->id_report;
            $this->VIEW->nameReport = $this->nameReport;
            $this->VIEW->extensionName = $this->extensionName;
        }
    }
    public function defineModelVariable()
    {

        if (is_object ($this->MODEL)) {
//            $this->VIEW->id_report = $this->id_report;
            $this->MODEL->nameReport = $this->nameReport;
            $this->MODEL->extensionName = $this->extensionName;
        }
    }
}