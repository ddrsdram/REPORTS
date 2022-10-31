<?php
namespace Reports\ForPopulation\InformationBillBigRSO_Small;

class Control extends \Reports\reportControl
{


    function __construct($id_report)
    {
        parent::__construct($id_report);

        $this->nameReport = "Извещение на половину страници РСО";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Извещение на половину страници РСО";
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
        $report->setSizePixelForQrCode(185);
        $report->setQrCodeOffsetX(30);
        $report->setQrCodeOffsetY(2);

        $report->setExcelPatternPath(__DIR__);
        $report->setExcelPatternName('InformationBillBigRSO_Small.xlsx');
        $report->setResultFileName($this->id_report);
        $report->setH($headArray);
        $report->setArray("t1",$data);
        $report->setArray("t2",Array());
        $report->setArray("t3",Array());
        $report->setArray("t4",Array());
        $report->setArray("t5",Array());
        $report->setDefaultRowHeight(12);
        if ($headArray['PDF'] == 'PDF'){
            $report->saveToPdf();
        }
        $report->run();
        //sleep(10);
        // TODO: Implement run() method.
    }
}