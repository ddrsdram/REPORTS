<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */


namespace Reports\ForPopulation\InformingBillVega;
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

    private $columnsPattern;
    private $rowsPattern;
    private $gRow;

    private $dataAllLS;
    private $dataLS;
    private $DH;
    function __construct()
    {
        $path = $_SERVER['DOCUMENT_ROOT'];
        $this->resultFilePath = $path."/ImpExp";
        $this->excelPatternName = 'pattern.xlsx';
    }

    public function run()
    {
        $this->gRow = 0;
        $this->defineSheetResult();
        $this->openPatternFile();
        $this->copyHeadFile();
        $this->copyColumnWidth();
        foreach ($this->dataAllLS as $this->dataLS){
            $this->copyMergeCells();
            $this->copyStyleXFCollection();
            $this->copyStyle();
            $this->copyData();
            $this->insertData();
            $this->gRow = $this->gRow + $this->rowsPattern;
        }
        $this->saveFile();
    }

    private function insertData()
    {
        $D = $this->dataLS;
        $row = 1;
        $this->insertValue($row,1,"Лицевой Сч.№ ".$D['id_LS']);
        $row ++;
        $this->insertValue($row,1,"Адрес:  {$D['status_street']} {$D['name_street']} д.{$D['house']} кв.{$D['room']}");
        $row ++;
        $this->insertValue($row,1,$this->DH['name_organization']);
        $row ++;
        $this->insertValue($row,1,"Тел. приемная ".$this->DH['telephone1']);
        $row ++;
        $this->insertValue($row,1,"Абонентский сектор ".$this->DH['telephone2']);
        $row ++;
        $this->insertValue($row,1,"№ лицензии  от ".$this->DH['OGRN']);
        $row ++;
        $this->insertValue($row,1,"ОГРН ".$this->DH['OGRN']);
        $row ++;
        $this->insertValue($row,1,"ИНН/КПП ".$this->DH['INN']."/".$this->DH['KKP']);
        $row ++;
        $this->insertValue($row,1,"Р/Сч {$this->DH['RSCH']}");
        $row ++;
        $this->insertValue($row,1,"К/Сч {$this->DH['KSCH']}");
        $row ++;
        $this->insertValue($row,1,"Бик  {$this->DH['BIK']}");
        $row ++;
        $this->insertValue($row,1,"Банк  {$this->DH['name_bank']}");

        $this->insertValue(26,1,"Лицевой Сч.№ ".$D['id_LS']);
        $this->insertValue(27,1,$this->DH['name_organization']);
        $this->insertValue(28,1,"Адрес:  {$D['status_street']} {$D['name_street']} д.{$D['house']} кв.{$D['room']}");

        $this->insertValue(1,3,"За {$D['name_month']}");
        $this->insertValue(1,9,$D['id_LS']);
        $this->insertValue(3,3,"Адрес:  {$D['status_street']} {$D['name_street']} д.{$D['house']} кв.{$D['room']}");

        $this->insertValue(5,9,$D['person']);
        $this->insertValue(6,9,$D['person_owner']);
        $this->insertValue(7,9,$D['soc_norm']);
        $this->insertValue(8,9,$D['person_tmp']);
        $this->insertValue(9,9,$D['area']);

        $this->insertMainTable(3,11,11,$this->dataLS['table'],array(3=>"name_type_accrual",6=>"value",7=>"tarif1",8=>"summa",9=>"recalc",10=>"coefficient",11=>"total"));
        $this->insertTable(3,9,29,$this->dataLS['total'],array(3=>"name_type_accrual",8=>"summa"));
    }

    private function insertValue($row,$col,$value)
    {
        $cells = Coordinate::stringFromColumnIndex($col).(string)($row+$this->gRow);
        $this->SheetResult->setCellValue($cells, $value);
/*
        $style = $this->SheetPattern->getStyleByColumnAndRow ($col, $row);
        $dstCell = Coordinate::stringFromColumnIndex( $col) . (string)($row + $this->gRow);
        $this->SheetResult->duplicateStyle($style, $dstCell);
*/
    }

    private function insertSumValue($row,$col,$rows)
    {
        $c = Coordinate::stringFromColumnIndex($col);
        $cells = $c.(string)($row+$this->gRow);
        $r1 = $row+$this->gRow - 1;
        $r2 = $row+$this->gRow - ($rows);
        $formula = "=SUM($c$r1:$c$r2)";
        $this->SheetResult->setCellValue($cells, $formula);

    }

    private function insertTable($colS,$colE,$rowBody,$dataArray,$columnName = Array())
    {
  /*      $range1 = Coordinate::stringFromColumnIndex($colS) . (string)($rowBody);
        $range2 = Coordinate::stringFromColumnIndex($colE) . (string)($rowBody);
        //$style = $this->SheetPattern->getStyle("$range1")->getBorders()->getTop();
  */
        $i = 0;
        foreach ($dataArray as $key => $value){
          //  $range1 = Coordinate::stringFromColumnIndex($colS) . (string)($rowBody + $this->gRow + $i);
//            $range2 = Coordinate::stringFromColumnIndex($colE) . (string)($rowBody + $this->gRow + $i);
            //$this->SheetResult->duplicateStyle($style, "$range1:$range2");
            //$this->SheetResult->duplicateStyle($style, "$range1");

            foreach ($columnName as $col => $nameField){
                $this->insertValue($rowBody + $i,$col,$value[$nameField]);
            }
            $i ++;
        }

    }

    private function insertMainTable($colS,$colE,$rowBody,$dataArray,$columnName = Array())
    {
        $i = 0;
        foreach ($dataArray as $key => $value){
            foreach ($columnName as $col => $nameField){
                $this->insertValue($rowBody + $i,$col,$value[$nameField]);
            }
            $i ++;
        }
        $this->insertSumValue($rowBody + $i,8,$i);
        $this->insertSumValue($rowBody + $i,9,$i);
        $this->insertSumValue($rowBody + $i,10,$i);
        $this->insertSumValue($rowBody + $i,11,$i);
    }

    public function setDataHead($arrayDataHead)
    {
        $this->DH = $arrayDataHead;
    }
    /**
     * @param mixed $dataAllLS
     */
    public function setDataAllLS($dataAllLS)
    {
        $this->dataAllLS = $dataAllLS;
    }

    /**
     * @param mixed $rowsPattern
     */
    public function setRowsPattern($rowsPattern)
    {
        $this->rowsPattern = $rowsPattern;
    }


    /**
     * @param mixed $columnsPattern
     */
    public function setColumnsPattern($columnsPattern)
    {
        $this->columnsPattern = $columnsPattern;
    }


    /**
     * @param $excelPatternName
     */
    public function setExcelPatternName($excelPatternName)
    {
        $this->excelPatternName = $excelPatternName;
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


    /**
     * @param mixed $resultFilePath
     */
    public function setResultFilePath($resultFilePath)
    {
        $this->resultFilePath = $resultFilePath;
    }

    public function defineSheetResult()
    {
        $this->spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $styleArray = array(
            'font'  => array(
                'size'  => 8,
                'name'  => 'Arial'
            ));
        $this->spreadsheet->getDefaultStyle()
            ->applyFromArray($styleArray);
        $this->SheetResult = $this->spreadsheet->getActiveSheet();
        $this->SheetResult->getDefaultRowDimension()->setRowHeight(12);
    }

    private function copyData()
    {
        $copyToRow = $this->gRow + 1;
        $range = Coordinate::stringFromColumnIndex($this->columnsPattern) . (string)($this->rowsPattern);
        $cellValues = $this->SheetPattern->rangeToArray("A1:$range");
        $this->SheetResult->fromArray($cellValues, null, "A$copyToRow");
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

    public function openPatternFile()
    {
        $BoockPattern = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $BoockPattern->setReadDataOnly(false);
        $loadFile = "$this->excelPatternPath/$this->excelPatternName";
        $this->BoockPattern = $BoockPattern->load($loadFile);
        $this->BoockPattern->setActiveSheetIndex(0);
        $this->SheetPattern = $this->BoockPattern->getActiveSheet();
    }

    public function saveFile()
    {
        $fileName = "$this->resultFilePath/$this->resultFileName.xlsx";
        unlink ($fileName);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->spreadsheet, "Xlsx");
        $writer->save($fileName);
    }


    public function copyStyleXFCollection() {

        $collection = $this->BoockPattern->getCellXfCollection();

        foreach ($collection as $key => $item) {
            $this->spreadsheet->addCellXf($item);
        }
    }


    private function copyMergeCells()
    {

        foreach ($this->SheetPattern->getMergeCells() as $mergeCell) {
            $mc = explode(":", $mergeCell);
            $mergeColSrcStart = Coordinate::columnIndexFromString(preg_replace("/[0-9]*/", "", $mc[0]));
            $mergeColSrcEnd = Coordinate::columnIndexFromString(preg_replace("/[0-9]*/", "", $mc[1]));
            $mergeRowSrcStart = ((int)preg_replace("/[A-Z]*/", "", $mc[0]));
            $mergeRowSrcEnd = ((int)preg_replace("/[A-Z]*/", "", $mc[1]));


            $relativeColStart = $mergeColSrcStart ;
            $relativeColEnd = $mergeColSrcEnd ;
            $relativeRowStart = $mergeRowSrcStart + $this->gRow;
            $relativeRowEnd = $mergeRowSrcEnd + $this->gRow;


            $targetColStart = Coordinate::stringFromColumnIndex($relativeColStart);
            $targetColEnd = Coordinate::stringFromColumnIndex( $relativeColEnd);
            $targetRowStart =  $relativeRowStart;
            $targetRowEnd =  $relativeRowEnd;

            $merge = (string)$targetColStart . (string)($targetRowStart) . ":" . (string)$targetColEnd . (string)($targetRowEnd);
            //Merge target cells
            $this->SheetResult->mergeCells($merge);
        }
    }


    private function copyStyle()
    {
        for ($row = 1; $row <= $this->rowsPattern; $row++) {
            for ($col = 1; $col <= $this->columnsPattern; $col++) {
//                $cell = $sheet->getCellByColumnAndRow($col, $row);
                $style = $this->SheetPattern->getStyleByColumnAndRow ($col, $row);
                $dstCell = Coordinate::stringFromColumnIndex( $col) . (string)($row + $this->gRow);
                $this->SheetResult->duplicateStyle($style, $dstCell);
            }
            $h = $this->SheetPattern->getRowDimension($row)->getRowHeight();
            $this->SheetResult->getRowDimension($row + $this->gRow)->setRowHeight($h);
        }
    }
    private function copyColumnWidth()
    {
        for($col = 1; $col<=$this->columnsPattern; $col++){
            $this->SheetResult->getColumnDimension(Coordinate::stringFromColumnIndex($col))->setWidth(
                $this->SheetPattern->getColumnDimension(Coordinate::stringFromColumnIndex($col))->getWidth());
        }
    }
}