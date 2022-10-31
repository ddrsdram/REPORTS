<?php
namespace Reports\ForPopulation\InformationBill_UK;

class Control extends \Reports\reportControl
{


    function __construct($id_report)
    {
        parent::__construct($id_report);

        $this->nameReport = "Извещение УК (3 на лист) ";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Извещение УК (3 на лист) ";
        $this->manageTable = 'list_LS_Bill';

        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();
    }

    public function run()
    {
        $headArray = $this->MODEL->getHeadArray();
        if ($headArray['nom_month']+1 == 13){
            $headArray['next_nom_month'] = 1 ;
            $headArray['next_year'] = $headArray['year'] + 1 ;
        }else{
            $headArray['next_nom_month'] = $headArray['nom_month']+1;
            $headArray['next_year'] = $headArray['year'] ;
        }


        $this->MODEL->nameReport .= $headArray['addNameFile'];
        if ($headArray['PDF'] == 'PDF'){
            $this->extensionName = ".pdf";
            $this->defineModelVariable();
        }
        $this->MODEL->updateReports_register();
        $data = $this->MODEL->getDataArray();
        $report = new \models\ReportOnPattern();

        $report->setExcelPatternPath(__DIR__);
        $report->setExcelPatternName('InformationBill_UK.xlsx');
        $report->setResultFileName($this->id_report);
        $report->setH($headArray);
        $report->setArray("t1",$data);
        $report->setArray("t2",Array());
        $report->setArray("t3",Array());
        $report->setArray("t4",Array());
        $report->setArray("t5",Array());
        if ($headArray['PDF'] == 'PDF'){
            $report->saveToPdf();
        }
        $report->setDefaultRowHeight(12);
        $report->run();
        //sleep(10);
        // TODO: Implement run() method.
    }
}