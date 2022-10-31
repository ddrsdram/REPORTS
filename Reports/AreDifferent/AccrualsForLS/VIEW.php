<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */


namespace Reports\AreDifferent\AccrualsForLS;
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
        $this->gRow = 0;
        $this->openPatternFile();
        $this->defineSheetResult();
        $this->copyHeadFile();

        $this->createHeadTable();
        $this->gRow += 5;

        foreach ($this->data as $id_LS => $data){
            $this->insertRow($data);
            $this->gRow += 5;
        }

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

        $this->widthColumn($col,12.57);
        $this->insertValue($col,1,$DA['id_LS']);
        $this->mergeCel($col,1,$col,5);
        $col ++;

        $this->widthColumn($col,12.57);
        $house = $DA['house']?' д.'.$DA['house']:'';
        $room = $DA['room']?' кв.'.$DA['room']:'';
        $address = $DA['name_street'].$house.$room;
        $this->insertValue($col,1,$address);
        $this->mergeCel($col,1,$col,5);
        $col ++;

        $this->widthColumn($col,4.57);
        $this->insertValue($col,1,$DA['area']);
        $this->mergeCel($col,1,$col,5);
        $col ++;

        $this->widthColumn($col,10);
        $this->insertValue($col,1,$DA['person']);
        $this->insertValue($col,2,$DA['soc_norm']);
        $this->insertValue($col,3,$DA['person_act']);
        $this->insertValue($col,4,$DA['person_owner']);
        $this->insertValue($col,5,$DA['person_tmp']);
        $col ++;

        $this->widthColumn($col,11.3);
        $this->insertValue($col,1,$DA['saldo_start']);
        $this->insertValue($col,2,$DA['accruals']);
        $this->insertValue($col,3,$DA['recalculate']);
        $this->insertValue($col,4,$DA['payment']);
        $this->insertValue($col,5,$DA['saldo_end']);
        $col ++;

        $this->setBorder(1,1,$col-1,5);

        $DT = $DA['table'];
        foreach ($DT as $key => $DR){
            $row = 1;
            $columns = $DR['detailing_general_report'];
            $this->widthColumn($col,9.57);
            if ($columns>1)
                $this->widthColumn($col+1,9.57);
            if ($columns>2)
                $this->widthColumn($col+2,9.57);

            if ($head){
                $this->mergeCel($col,1,$col + ($columns-1),1);
                $this->insertValue($col,$row,$DR['name']);
                $row ++;
            }
            $this->insertValue($col,$row+0,$DR['tarif1']);
            $this->insertValue($col,$row+1,$DR['summa1']);
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

            $col += $columns;
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