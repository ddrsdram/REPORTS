<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */


namespace Reports\ByTypeAccrual\AccrualsForLS_OneRow;
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


    private  $typeAccrual;

    function __construct()
    {
        $path = $_SERVER['DOCUMENT_ROOT'];
        $this->resultFilePath = $path."/ImpExp";
        $this->excelPatternName = 'pattern.xlsx';
    }

    public function run()
    {
        $this->gRow = 0;
        $this->openPatternFile();
        $this->defineSheetResult();
        $this->copyHeadFile();

        $this->createHeadTable();
        $this->gRow += 2;

        foreach ($this->data as $id_LS => $data){
            $this->insertRow($data);
            $this->gRow += 1;
        }
        $this->SheetResult->freezePane('C3');

        $this->saveFile();
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
        $this->SheetResult = $this->spreadsheet->getActiveSheet();
    }


    public function saveFile()
    {
        $fileName = "$this->resultFilePath/$this->resultFileName.xlsx";
        unlink ($fileName);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->spreadsheet, "Xlsx");
        $writer->save($fileName);
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

    private function createHeadTable()
    {
        $DA = Array();
        $DA['id_LS'] = 'Лицевой Счет';
        $DA['name_street'] = 'Адрес';
        $DA['house'] = false;
        $DA['room'] = false;
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
            $arr['name']            = $res['name'];
            $arr['saldo_start']     = 'Сальдо на начало';
            $arr['summa']           = 'Начислено';
            $arr['recalculate']     = 'Справка';
            $arr['payments']        = 'Оплата';
            $arr['saldo_end']       = 'Сальдо на конец';
            $DA['table'][] = $arr;
        }

        $this->insertRow($DA,true);
    }

    private function insertRow($DA = Array(),$head = false)
    {

        $col = 1;

        if ($head) {
            $this->mergeCel($col,1,$col,2);
            $this->widthColumn($col, 12.57);
        }
        $this->insertValue($col,1,$DA['id_LS']);
        $col ++;

        if ($head) {
            $this->mergeCel($col,1,$col,2);
            $this->widthColumn($col, 12.57);
        }
        $house = $DA['house']?' д.'.$DA['house']:'';
        $room = $DA['room']?' кв.'.$DA['room']:'';
        $address = $DA['name_street'].$house.$room;
        $this->insertValue($col,1,$address);
        $col ++;

        if ($head){
            $this->mergeCel($col,1,$col,2);
            $this->widthColumn($col,4.57);
        }
        $this->insertValue($col,1,$DA['area']);
        $col ++;

        if ($head) {
            $this->mergeCel($col,1,$col,2);
            $this->widthColumn($col, 10);
        }
        $this->insertValue($col,1,$DA['person']);
        $col ++;

        if ($head) {
            $this->mergeCel($col,1,$col,2);
            $this->widthColumn($col, 10);
        }
        $this->insertValue($col,1,$DA['soc_norm']);
        $col ++;

        if ($head) {
            $this->mergeCel($col,1,$col,2);
            $this->widthColumn($col, 10);
        }
        $this->insertValue($col,1,$DA['person_act']);
        $col ++;

        if ($head) {
            $this->mergeCel($col,1,$col,2);
            $this->widthColumn($col, 10);
        }
        $this->insertValue($col,1,$DA['person_owner']);
        $col ++;

        if ($head) {
            $this->mergeCel($col,1,$col,2);
            $this->widthColumn($col, 10);
        }
        $this->insertValue($col,1,$DA['person_tmp']);
        $col ++;

        if ($head) {
            $this->mergeCel($col,1,$col,2);
            $this->widthColumn($col, 11.3);
        }
        $this->insertValue($col,1,$DA['saldo_start']);
        $col ++;

        if ($head) {
            $this->mergeCel($col,1,$col,2);
            $this->widthColumn($col, 10);
        }
        $this->insertValue($col,1,$DA['accruals']);
        $col ++;

        if ($head) {
            $this->mergeCel($col,1,$col,2);
            $this->widthColumn($col, 10);
        }
        $this->insertValue($col,1,$DA['recalculate']);
        $col ++;

        if ($head) {
            $this->mergeCel($col,1,$col,2);
            $this->widthColumn($col, 10);
        }
        $this->insertValue($col,1,$DA['payment']);
        $col ++;

        if ($head) {
            $this->mergeCel($col,1,$col,2);
            $this->widthColumn($col, 10);
        }
        $this->insertValue($col,1,$DA['saldo_end']);
        $col ++;

        $this->setBorder(1,1,$col-1,1);

        $DT = $DA['table'];
        foreach ($DT as $key => $DR){
            $row = 1;

            if ($head){
                $this->widthColumn($col,9.57);
                $this->mergeCel($col,1,$col+4,1);
                $this->insertValue($col,$row,$DR['name']);
                $this->setBorder($col,1, $col+4,2);
                $row ++;
            }else{
                $this->setBorder($col,1, $col+4,1);
            }
            if ($head)
                $this->widthColumn($col,9.57);
            $this->insertValue($col,$row,$DR['saldo_start']);
            $col ++;

            if ($head)
                $this->widthColumn($col,9.57);
            $this->insertValue($col,$row,$DR['summa']);
            $col ++;

            if ($head)
                $this->widthColumn($col,9.57);
            $this->insertValue($col,$row,$DR['recalculate']);
            $col ++;

            if ($head)
                $this->widthColumn($col,9.57);
            $this->insertValue($col,$row,$DR['payments']);
            $col ++;

            if ($head)
                $this->widthColumn($col,9.57);
            $this->insertValue($col,$row,$DR['saldo_end']);
            $col ++;



        }
        //
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


    private function widthColumn($column,$width)
    {
        $cells = Coordinate::stringFromColumnIndex($column);
        $this->SheetResult->getColumnDimension($cells)->setWidth($width);
        $this->SheetResult->getColumnDimension($cells)->setAutoSize(true);
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