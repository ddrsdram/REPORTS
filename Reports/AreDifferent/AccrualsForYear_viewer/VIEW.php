<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */


namespace Reports\AreDifferent\AccrualsForYear_viewer;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;


class VIEW extends \Reports\reportView
{

    /**
     * @var \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     */

    private $SheetPattern;



    /**
     * @var \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    

    /**
     * @var \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    private $BoockPattern;
    private $excelPatternPath;
    private $excelPatternName;

    private $gRow;

    /**
     * @var \backend\Connection;
     */
    private  $typeAccrual;

    function __construct()
    {
        $path = $_SERVER['DOCUMENT_ROOT'];
        $this->resultFilePath = $path."/ImpExp";
        $this->excelPatternName = 'pattern.xlsx';
    }

    public function run()
    {

        //$this->openPatternFile();
        $this->defineSheetResult();
        //$this->copyHeadFile();
        $this->headSettings =  current($this->data);
        $this->gRow = 0;
        $this->createHeadReport();



        $this->gRow = 3;
        $this->createHeadTable();
        $this->gRow += 1;

        foreach ($this->data as $id_LS => $data){
            $this->insertRow($data);
            $this->gRow += 5;
        }

       // $this->SheetResult->freezePane('C9');
        $this->saveFile();
    }


    /**
     * @param mixed $headSettings
     */
    public function setHeadSettings($headSettings)
    {
        $this->headSettings = $headSettings;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }


    /**
     * @param mixed $typeAccrual
     */
    public function setTypeAccrual($typeAccrual)
    {
        $this->typeAccrual = $typeAccrual;
    }


    public function defineSheetResult()
    {
        $this->spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $styleArray = array(
            'font'  => array(
                'size'  => 10,
                'name'  => 'Arial Cyr'
            ));
        $this->spreadsheet->getDefaultStyle()
            ->applyFromArray($styleArray);
        /*
                */
        $this->SheetResult = $this->spreadsheet->getActiveSheet();

        $this->SheetResult->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $this->SheetResult->getPageSetup()->setFitToWidth(1);
        $this->SheetResult->getPageSetup()->setFitToHeight(0);
        $this->SheetResult->getPageMargins()->setTop(0.4);
        $this->SheetResult->getPageMargins()->setRight(0.4);
        $this->SheetResult->getPageMargins()->setLeft(0.72);
        $this->SheetResult->getPageMargins()->setBottom(0.4);
    }


    public function saveFile()
    {
        $fileName = "$this->resultFilePath/$this->resultFileName.xlsx";
        unlink ($fileName);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->spreadsheet, "Xlsx");
        $writer->save($fileName);
        /*
        $fileName = "$this->resultFilePath/$this->resultFileName.Html";
        unlink ($fileName);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->spreadsheet, "Html");
        $writer->save($fileName);
        */
    }

    public function openPatternFile()
    {
        $BoockPattern = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $BoockPattern->setReadDataOnly(false);
        $loadFile = "$this->excelPatternPath/$this->excelPatternName";
        $this->BoockPattern = $BoockPattern->load($loadFile);
        $this->BoockPattern->setActiveSheetIndex(0);
        $this->SheetPattern = $this->BoockPattern->getActiveSheet();
    }

    private function copyHeadFile()
    {
        $this->SheetResult->getPageSetup()->setFitToPage(           $this->SheetPattern->getPageSetup()->getFitToPage()) ;
        $this->SheetResult->getPageSetup()->setScale(               $this->SheetPattern->getPageSetup()->getScale()) ;
        $this->SheetResult->getPageSetup()->setFitToWidth(          $this->SheetPattern->getPageSetup()->getFitToWidth()) ;
        $this->SheetResult->getPageSetup()->setFitToHeight(         $this->SheetPattern->getPageSetup()->getFitToHeight()) ;
        $this->SheetResult->getPageSetup()->setOrientation(         $this->SheetPattern->getPageSetup()->getOrientation()) ;
        $this->SheetResult->getPageSetup()->setHorizontalCentered(  $this->SheetPattern->getPageSetup()->getHorizontalCentered()) ;
        $this->SheetResult->getPageSetup()->setVerticalCentered(    $this->SheetPattern->getPageSetup()->getVerticalCentered()) ;


        $this->SheetResult->getPageMargins()->setTop(       $this->SheetPattern->getPageMargins()->getTop()) ;
        $this->SheetResult->getPageMargins()->setBottom(    $this->SheetPattern->getPageMargins()->getBottom()) ;
        $this->SheetResult->getPageMargins()->setFooter(    $this->SheetPattern->getPageMargins()->getFooter()) ;
        $this->SheetResult->getPageMargins()->setHeader(    $this->SheetPattern->getPageMargins()->getHeader()) ;
        $this->SheetResult->getPageMargins()->setLeft(      $this->SheetPattern->getPageMargins()->getLeft()) ;
        $this->SheetResult->getPageMargins()->setRight(     $this->SheetPattern->getPageMargins()->getRight()) ;
    }


    private function createHeadReport()
    {

        $LS = $this->headSettings['id_LS'];
        $street = $this->headSettings['name_street'];
        $house = $this->headSettings['house'];
        $room = $this->headSettings['room'];
        $JEU = $this->headSettings['name_JEU'];
        $fam = $this->headSettings['fam'];
        $im = $this->headSettings['im'];
        $ot = $this->headSettings['ot'];
        $this->insertValue(1,1,'Начисления');
        $this->insertValue(1,2,"Лицевой счет:$LS Адрес: Улица $street, дом $house, квартира $room, ЖЭУ ($JEU)");
        $this->insertValue(1,3,"Основной владелец  - $fam $im $ot");


    }


    private function createHeadTable()
    {
        $DA = Array();
        $DA['name_month'] = 'Месяц';
        $DA['area'] = 'Площадь';
        $DA['person'] = 'Прописано';
        $DA['soc_norm'] = 'Соц.норм';
        $DA['person_act'] = 'По акту';
        $DA['person_owner'] = 'Владецев.';
        $DA['person_tmp'] = 'Врем.проп.';
        $DA['saldo_start'] = 'На начало';
        $DA['accruals'] = 'Начисление';
        $DA['recalculate'] = 'Справка';
        $DA['payment'] = 'Оплата';
        $DA['saldo_end'] = 'На конец';
//        $DA[''] = '';
        while ($res = $this->typeAccrual->fetch()){
            $arr = Array();
            $arr['name'] = $res['name'];
            $arr['detailing_general_report'] = $res['detailing_general_report'];
            $arr['tarif1'] = 'Тариф';
            $arr['tarif2'] = 'Св. норм';
            $arr['tarif3'] = 'РСО';
            $arr['summa1'] = 'Начислено';
            $arr['summa2'] = '';
            $arr['summa3'] = '';
            $arr['recalculate'] = 'Справка';
            $arr['payments'] = 'Оплата';
            $DA['table'][] = $arr;
        }

        $this->insertRow($DA,true);
    }

    private function insertRow($DA = Array(),$head = false)
    {

        $col = 1;

        $this->widthColumn($col,12.57,false);
        $this->insertValue($col,1,$DA['name_month']);
        if (!$head)
            $this->mergeCel($col,1,$col,5);
        $col ++;

        $this->widthColumn($col,4.57);
        $this->insertValue($col,1,$DA['area']);
        if (!$head)
            $this->mergeCel($col,1,$col,5);
        $col ++;
        if ($head){
            $this->widthColumn($col,11.14,false);
            $this->insertValue($col,1,'Демография');
            $this->mergeCel($col,1,$col+1,1);
            $col ++;
            $this->widthColumn($col,2,false);
            $col ++;

            $this->widthColumn($col,12,false);
            $this->insertValue($col,1,'Основные итоги');
            $this->mergeCel($col,1,$col+1,1);
            $col ++;
            $this->widthColumn($col,13,false);
            $col ++;


            $col ++;
            $this->setBorder(1,1,$col-1,1);
            $this->heightRow($this->gRow+1,94.5);
        }else{
            $this->insertValue($col,1,'Прописано');
            $this->insertValue($col,2,'Соц.норм');
            $this->insertValue($col,3,'По акту');
            $this->insertValue($col,4,'Владецев.');
            $this->insertValue($col,5,'Врем.проп.');
            $col ++;
            $this->insertValue($col,1,$DA['person']);
            $this->insertValue($col,2,$DA['soc_norm']);
            $this->insertValue($col,3,$DA['person_act']);
            $this->insertValue($col,4,$DA['person_owner']);
            $this->insertValue($col,5,$DA['person_tmp']);
            $col ++;


            $this->insertValue($col,1,'На начало');
            $this->insertValue($col,2,'Начисление');
            $this->insertValue($col,3,'Справка');
            $this->insertValue($col,4,'Оплата');
            $this->insertValue($col,5,'На конец');
            $col ++;
            $this->insertValue($col,1,$DA['saldo_start']);
            $this->insertValue($col,2,$DA['accruals']);
            $this->insertValue($col,3,$DA['recalculate']);
            $this->insertValue($col,4,$DA['payment']);
            $this->insertValue($col,5,$DA['saldo_end']);
            $col ++;
            $this->setBorder(1,1,$col-1,5);
        }


        $DT = $DA['table'];
        $row = 1;

        if (!$head){
            $this->insertValue($col,$row+0,'Тариф');
            $this->insertValue($col,$row+1,'Начислено');
            $this->insertValue($col,$row+2,'Справка');
            $this->insertValue($col,$row+3,'Оплата');
            $this->setBorder($col+0,$row+0,$col+0,$row+4);
            $col ++;
        }
        foreach ($DT as $key => $DR){
            $columns = $DR['detailing_general_report'];

            if ($head){
                $this->mergeCel($col,1,$col + ($columns-1),1);
                $this->insertValue($col,$row,$DR['name']);

                $this->setBorder($col,1,$col + ($columns-1),1);
            }else{
                $this->insertValue($col,$row+0,$DR['tarif1']);
                if ($columns == 1){
                    $this->insertValue($col,$row+1,$DR['summa']);
                }else{
                    $this->insertValue($col,$row+1,$DR['summa1']);

                }
                $this->insertValue($col,$row+2,$DR['recalculate']);
                $this->insertValue($col,$row+3,$DR['payments']);
                if ($columns>1){
                    $this->insertValue($col+1,$row+0,$DR['tarif2']);
                    $this->insertValue($col+1,$row+1,$DR['summa2']);
                }
                if ($columns>2){
                    $this->insertValue($col+2,$row+0,$DR['tarif3']);
                    $this->insertValue($col+2,$row+1,$DR['summa3']);
                }
                $this->setBorder($col,1,$col + ($columns-1),5);
            }

            $this->widthColumn($col,9.57);
            if ($columns>1)
                $this->widthColumn($col+1,9.57);
            if ($columns>2)
                $this->widthColumn($col+2,9.57);

            $col += $columns;
        }
        if ($head){

            $this->SheetResult->getStyle("A4:BZ4")->getFont()->setSize(8)->setBold(1);
            $this->SheetResult->getStyle("A4:G4")->getAlignment()->setVertical("center");

            $this->SheetResult->getStyle("A4:BZ4")->getAlignment()->setHorizontal("center");

            $this->SheetResult->getStyle("H4:BZ4")->getAlignment()->setTextRotation(90);
            $this->SheetResult->getStyle("H4:BZ4")->getAlignment()->setWrapText(true);
            $this->SheetResult->getStyle("H4:BZ4")->getAlignment()->setVertical("bottom");
        }

    }

    /**
     * @param $cStart
     * @param $rStart
     * @param $cEnd
     * @param $rEnd
     */
    private function mergeCel($cStart,$rStart,$cEnd,$rEnd)
    {
        $range1 = Coordinate::stringFromColumnIndex($cStart) . (string)($this->gRow+$rStart);
        $range2 = Coordinate::stringFromColumnIndex($cEnd) . (string)($this->gRow+$rEnd);
        $this->SheetResult->mergeCells("$range1:$range2");

    }


    private function insertValue($column,$row,$value)
    {
        $cells = Coordinate::stringFromColumnIndex($column).(string)($this->gRow+$row);
        $this->SheetResult->setCellValue($cells, $value);

    }


    private function widthColumn($column,$width,$autoSize = true)
    {
        $cells = Coordinate::stringFromColumnIndex($column);
        $this->SheetResult->getColumnDimension($cells)->setWidth($width);
        $this->SheetResult->getColumnDimension($cells)->setAutoSize($autoSize);
    }
    private function heightRow($row,$height,$autoSize = false)
    {

        $this->SheetResult->getRowDimension($row)->setRowHeight($height);
        //$this->SheetResult->getColumnDimension($row)->setAutoSize($autoSize);
    }
    private function setBorder($cStart,$rStart,$cEnd,$rEnd)
    {
        $range1 = Coordinate::stringFromColumnIndex($cStart) . (string)($this->gRow+$rStart);
        $range2 = Coordinate::stringFromColumnIndex($cEnd) . (string)($this->gRow+$rEnd);
        $styleThinBlackBorderOutline = array(
            'borders' => array(
                'outline' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => 'FF000000'),
                ),
                'inside' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR,
                    'color' => array('argb' => 'FF000000'),
                ),
            ),
        );

        $this->SheetResult->getStyle("$range1:$range2")->applyFromArray($styleThinBlackBorderOutline);

    }

    /**
     * @param $excelPatternPath
     */
    public function setExcelPatternPath($excelPatternPath)
    {
        $this->excelPatternPath = $excelPatternPath;
    }


    /**
     * @param mixed $resultFileName
     */
    public function setResultFileName($resultFileName)
    {
        $this->resultFileName = $resultFileName;
    }


}