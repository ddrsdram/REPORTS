<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */


namespace Reports\AreDifferent\Report2;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;


class VIEW extends \Reports\reportView
{
    private $sumColumn;
    private $JEU_Array;
    private $typeAccrual_Array;
    


    /**
     * @var \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
     */

    private $headSettings;
    private $globalRow;
    private $gRow,$gCol;

    function __construct()
    {
        $path = $_SERVER['DOCUMENT_ROOT'];
        $this->resultFilePath = $path."/ImpExp";
        $this->globalRow = 1;
    }


    /**
     * @param mixed $headSettings
     */
    public function setHeadSettings($headSettings)
    {
        $this->headSettings = $headSettings;
    }


    /**
     * @param mixed $nameReport
     */
    public function setNameReport($nameReport)
    {
        $this->nameReport = $nameReport;
    }


    /**
     * @param mixed $data_array
     */
    public function setDataArray($data_array)
    {
        $this->typeAccrual_Array = $data_array['typeAccrual'];
        $this->JEU_Array = $data_array['totals'];
        $this->sumColumn = $data_array['columns'];
    }


    public function createReport()
    {
        $this->SheetResult = $this->defineSheetResult();

        $this->createHeadReport();
        $this->createBodyReport();

        $this->saveFile();
    }


    /**
     * @return \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     */
    public function defineSheetResult()
    {
        $this->spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        return  $this->spreadsheet->getActiveSheet();
    }


    public function saveFile()
    {
        $fileName = "$this->resultFilePath/$this->nameReport";
        unlink ($fileName);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->spreadsheet, "Xlsx");
        $writer->save($fileName);
    }


    /**
     * @param $this->SheetResult \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     */
    public function createBodyReport()
    {
        $format = '# ### ##0.00;[Red]# ### ##0.00';
        //$format = '0.00';

        //$this->sumColumn = $data_array['columns'];

        $this->gCol = 0;

        $this->gRow = $this->globalRow;
        $region = 0;
        $kol_region = 0;
        $rowRegion = Array();
        foreach ($this->JEU_Array  as $key => $row_JEU){
            if ($region != $row_JEU['region']){
                if ($region != 0) {
                    $rowRegion[]= ["row"=>$this->gRow,'name'=>$row_JEU['name_region']];
                    $this->gRow ++;
                }else{
                    $rowRegion[]= ["row"=>$this->gRow - 1,'name'=>$row_JEU['name_region']];
                }
                $kol_region ++;
                $region = $row_JEU['region'];
            }

            $this->setValue($this->gCol+1,$this->gRow,$row_JEU['name_JEU']);

            $this->setValue($this->gCol+2,$this->gRow,$row_JEU['debet_start']);
            $this->setValue($this->gCol+3,$this->gRow,$row_JEU['kredit_start']);

            $this->setValue($this->gCol+4+$this->sumColumn,$this->gRow,$row_JEU['summa_accruals_total']);
            $this->setValue($this->gCol+5+$this->sumColumn,$this->gRow,$row_JEU['summa_payment']);

            $this->setValue($this->gCol+6+$this->sumColumn,$this->gRow,$row_JEU['debet_end']);
            $this->setValue($this->gCol+7+$this->sumColumn,$this->gRow,$row_JEU['kredit_end']);

            $this->setStyle($this->gCol+2,$this->gRow,$format);
            $this->setStyle($this->gCol+3,$this->gRow,$format);
            $this->setStyle($this->gCol+4+$this->sumColumn,$this->gRow,$format);
            $this->setStyle($this->gCol+5+$this->sumColumn,$this->gRow,$format);
            $this->setStyle($this->gCol+6+$this->sumColumn,$this->gRow,$format);
            $this->setStyle($this->gCol+7+$this->sumColumn,$this->gRow,$format);

            $accruals = $row_JEU['data_type_accrual'];

            $col = 0;
            foreach ($this->typeAccrual_Array as $type_accrual => $valueArray){

                $summ = $accruals[$type_accrual];
                $this->setValue($this->gCol+4+$col,$this->gRow,$summ['summa1']);
                $this->setStyle($this->gCol+4+$col,$this->gRow,$format);
                $col += 1;
                if ($valueArray['detailing_general_report'] > 1){
                    $this->setValue($this->gCol+4+$col,$this->gRow,$summ['summa2']);
                    $this->setStyle($this->gCol+4+$col,$this->gRow,$format);
                    $col += 1;
                }
                if ($valueArray['detailing_general_report'] > 2){
                    $this->setValue($this->gCol+4+$col,$this->gRow,$summ['summa3']);
                    $this->setStyle($this->gCol+4+$col,$this->gRow,$format);
                    $col += 1;
                }

            }
            $this->gRow +=1;
        }

        if ($kol_region == 1)
            $this->row_EndTotal();
        else{
            $rowRegion[]= ["row"=>$this->gRow,'name'=>""];
            $this->gRow +=1;
            $this->row_EndTotal_For_multiRegion($rowRegion);
        }


        $this->bordersTable();

        $this->autoSizeColumns();
    }

    private function row_EndTotal()
    {
        $this->setValue(1,$this->gRow,"ИТОГО:");
        $dstCell = Coordinate::stringFromColumnIndex(1) . (string)($this->gRow);
        $this->SheetResult->getStyle($dstCell)->getFont()->setBold(true);

        $ColF = 2;
        while ($ColF <= $this->sumColumn + 7){

            $this->setStyle($ColF,$this->gRow,'# ### ### ##0.00;-[Red]# ### ##0.00');

            $dstCell = Coordinate::stringFromColumnIndex($ColF) . (string)($this->gRow);
            $C = Coordinate::stringFromColumnIndex($ColF);
            $rows =  $this->gRow-1;
            $this->SheetResult->setCellValue($dstCell, "=SUM({$C}5:{$C}{$rows})");
            $this->SheetResult->getStyle($dstCell)->getFont()->setBold(true);

            $ColF += 1;
        }
    }

    private function row_EndTotal_For_multiRegion($rowRegion)
    {
        $ColF = 2;
        while ($ColF <= $this->sumColumn + 7){

            $first = reset($rowRegion);
            $formula_bigRow = "=(";
            foreach ($rowRegion as $key => $second){
                if ($key < 1) continue;

                $this->setValue(1,$second['row'],"ИТОГО {$first['name']}:");
                $dstCell = Coordinate::stringFromColumnIndex(1) . (string)($second['row']);
                $this->SheetResult->getStyle($dstCell)->getFont()->setBold(true);


                $this->setStyle($ColF,$second['row'],'# ### ### ##0.00;-[Red]# ### ##0.00');
                $firstRow = $first['row'] + 1;
                $secondRow = $second['row'] - 1;

                $C = Coordinate::stringFromColumnIndex($ColF);
                $formula = "=SUM({$C}{$firstRow}:{$C}{$secondRow})";
                $dstCell = Coordinate::stringFromColumnIndex($ColF) . (string)($second['row']);
                $this->SheetResult->setCellValue($dstCell, $formula);
                $this->SheetResult->getStyle($dstCell)->getFont()->setBold(true);

                $formula_bigRow .= "$dstCell+";

                $first = $second;
            }
            $formula_bigRow = substr($formula_bigRow,0,-1).")";
            $dstCell = Coordinate::stringFromColumnIndex($ColF) . (string)($first['row'] + 1);
            $this->SheetResult->setCellValue($dstCell, $formula_bigRow);
            $this->SheetResult->getStyle($dstCell)->getFont()->setBold(true);
            $this->setStyle($ColF,$first['row'] + 1,'# ### ### ##0.00;-[Red]# ### ##0.00');
            $this->setValue(1,$first['row'] + 1,"ИТОГО:");
            $dstCell = Coordinate::stringFromColumnIndex(1) . (string)($first['row'] + 1);
            $this->SheetResult->getStyle($dstCell)->getFont()->setBold(true);

            $ColF += 1;
        }

    }

    private function bordersTable()
    {
        $C = Coordinate::stringFromColumnIndex($this->sumColumn + 7);
        $styleArray = array(
            'borders' => array(
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
                'left' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
                'inside' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ),
        );
        $this->SheetResult ->getStyle("A3:$C$this->gRow")->applyFromArray($styleArray);
    }

    private function autoSizeColumns()
    {
        $this->SheetResult->getColumnDimension(Coordinate::stringFromColumnIndex(1))->setAutoSize(true);
        for ($i= 2 ;$i <= $this->sumColumn + 7; $i++){
            //$this->SheetResult->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
            $this->SheetResult->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setWidth(13);
        }

    }
    /**
     * @param $this->SheetResult \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     */
    private function createHeadReport()
    {
        $this->gCol = 0;
        $columns = $this->gCol + $this->sumColumn + 7;
        $this->gRow = $this->globalRow;


        $range1 = Coordinate::stringFromColumnIndex($this->gCol+1) . (string)($this->gRow);
        $range2 = Coordinate::stringFromColumnIndex($columns) . (string)($this->gRow+3);
        $this->SheetResult->getStyle("$range1:$range2")->getAlignment()->setHorizontal('center');
        $this->SheetResult->getStyle("$range1:$range2")->getAlignment()->setVertical("center");

        $this->mergeCel($this->gCol+1,1,$columns,1);
        $this->setValue($this->gCol+1,$this->gRow,'Отчет о начислениях '.$this->headSettings['name_organization']);

        $this->mergeCel($this->gCol+1,2,$columns,2);
        $this->setValue($this->gCol+1,$this->gRow+1,"за ".$this->headSettings['full_name_month'].'г.');

        $this->gRow +=2;

        $this->SheetResult->getRowDimension($this->gRow+1)->setRowHeight(90);

        //$this->SheetResult->getColumnDimensionByColumn('A')->setAutoSize(true);


        $this->mergeCel($this->gCol+1,$this->gRow,$this->gCol+1,$this->gRow+1);
        $this->setValue($this->gCol+1,$this->gRow,'ЖЭУ');

        $this->mergeCel($this->gCol+2,$this->gRow,$this->gCol+3,$this->gRow);
        $this->setValue($this->gCol+2,$this->gRow,'Сальдо на начало');
        $this->setValue($this->gCol+2,$this->gRow+1,'Дебет');
        $this->setValue($this->gCol+3,$this->gRow+1,'Кредит');
        $this->mergeCel($this->gCol+4,$this->gRow,$this->gCol+$this->sumColumn+4,$this->gRow);
        $this->setValue($this->gCol+4,$this->gRow,'Виды начислений');

        foreach ($this->typeAccrual_Array as $key => $valueArray){

            $col = 0;
            $this->setValue($this->gCol+4,$this->gRow+1,$valueArray['name']);
            if ($valueArray['detailing_general_report'] > 1){
                $this->setValue($this->gCol+5,$this->gRow+1,$valueArray['name']. " сверх норматива");
                $col += 1;
            }
            if ($valueArray['detailing_general_report'] > 2){
                $this->setValue($this->gCol+6,$this->gRow+1,$valueArray['name']. " РСО");
                $col += 1;
            }
            $range1 = Coordinate::stringFromColumnIndex($this->gCol+4) . (string)($this->gRow+1);
            $range2 = Coordinate::stringFromColumnIndex($this->gCol+4+$col) . (string)($this->gRow+1);
            $this->SheetResult->getStyle("$range1:$range2")->getAlignment()->setTextRotation(90);
            $this->SheetResult->getStyle("$range1:$range2")->getAlignment()->setWrapText(true);
            $this->SheetResult->getStyle("$range1:$range2")->getAlignment()->setVertical("bottom");
            $this->gCol += ($col+1);
        }
        $this->gCol += 3;
        $this->setValue($this->gCol+1,$this->gRow+1,'Итого');
        $this->gCol += 1;
        $this->mergeCel($this->gCol+1,$this->gRow,$this->gCol+1,$this->gRow+1);
        $this->setValue($this->gCol+1,$this->gRow,'Оплата');

        $this->mergeCel($this->gCol+2,$this->gRow,$this->gCol+3,$this->gRow);
        $this->setValue($this->gCol+2,$this->gRow,'Сальдо на конец');
        $this->setValue($this->gCol+2,$this->gRow+1,'Дебет');
        $this->setValue($this->gCol+3,$this->gRow+1,'Кредит');


        $this->globalRow +=4;
    }


    /**
     * @param $this->SheetResult \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     * @param $col
     * @param $row
     * @param $val
     */
    private function setValue($col,$row,$val)
    {
        $dstCell = Coordinate::stringFromColumnIndex($col) . (string)($row);
        $this->SheetResult->setCellValue($dstCell, $val);

    }


    /**
     * @param $this->SheetResult \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     * @param $col
     * @param $row
     * @param $style
     */
    private function setStyle($col,$row,$style)
    {
        $dstCell = Coordinate::stringFromColumnIndex($col) . (string)($row);
        $this->SheetResult->getStyle($dstCell)->getNumberFormat()->setFormatCode($style); //'$ #,##0.00'

    }


    /**
     * @param $this->SheetResult \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     * @param $cStart
     * @param $rStart
     * @param $cEnd
     * @param $rEnd
     */
    private function mergeCel($cStart,$rStart,$cEnd,$rEnd)
    {
        $range1 = Coordinate::stringFromColumnIndex($cStart) . (string)($rStart);
        $range2 = Coordinate::stringFromColumnIndex($cEnd) . (string)($rEnd);
        $this->SheetResult->mergeCells("$range1:$range2");

    }

}