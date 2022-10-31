<?php
namespace Reports\AreDifferent\decryption_coldWater_byHouse2_house;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);

        $this->nameReport = "Расшифровка начислений по хололдной воде по домам";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Расшифровка начислений по хололдной воде по домам (Приложение № 7 к Соглашению № 40-юр  с 25.12.2020 г.)";
        $this->manageTable = 'list_LS_decoding_reports';


        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();
    }

    public function run()
    {
        $this->MODEL->updateReports_register();
        $id_user = $this->MODEL->getUser();

        $report = new \models\ReportOnPattern();
        $data1 = $this->MODEL->getDataTable($id_user,8);
        $report->setArray("t1",$data1);

        $headArray = $this->MODEL->getHeadArray();

        if ($data2 = $this->MODEL->getDataTable($id_user,12)){
            $report->setArray("t2",$data2);
            $headArray['table2'] = '1';
        }else{
            $headArray['table2'] = '0';
        }

        if ($data3 = $this->MODEL->getDataTable($id_user,13)){
            $report->setArray("t3",$data3);
            $headArray['table3'] = '1';
        }else{
            $headArray['table3'] = '0';
        }

        if ($data3 = $this->MODEL->getDataTable($id_user,15)){
            $report->setArray("t4",$data3);
            $headArray['table4'] = '1';
        }else{
            $headArray['table4'] = '0';
        }
        $report->setExcelPatternPath(__DIR__);
        $report->setExcelPatternName('decryption_coldWater_byHouse2_house.xlsx');
        $report->setResultFileName($this->id_report);
        $report->setH($headArray);

        $report->run();
        //sleep(10);
        // TODO: Implement run() method.
    }
}