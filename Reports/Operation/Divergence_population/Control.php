<?php
namespace Reports\Operation\Divergence_population;

class Control extends \Reports\reportControl
{

    function __construct($id_report)
    {
        parent::__construct($id_report);
        $this->nameReport = "Расхождение численности людей в сравнении с предыдущим месяцем";
        $this->extensionName = ".xlsx";
        $this->descriptionReport = "Расхождение численности людей в сравнении с предыдущим месяцем";
        $this->manageTable = 'list_LS_reports';

        $this->VIEW = new VIEW();
        $this->defineViewVariable();

        $this->MODEL = new MODEL($this->id_report);
        $this->defineModelVariable();
    }

    public function run()
    {
        $this->MODEL->updateReports_register();


        $report = new \models\ReportOnPattern();
        $report->setExcelPatternPath(__DIR__);
        $report->setExcelPatternName('Divergence_population.xlsx');

        $report->setExcelPatternPath(__DIR__);
        $report->setResultFileName($this->id_report);

        $report->setH($this->MODEL->getHeadArray());
        $t1 = $this->MODEL->get_Divergence_areas();
        $report->setArray("t1",$t1);
        $t2 = $this->MODEL->get_Divergence_areas_close();
        $report->setArray("t2",$t2);

        $report->printArea_enabled();
        $report->run();
        //sleep(10);
        // TODO: Implement run() method.
    }
}