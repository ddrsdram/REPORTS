<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */


namespace Reports\Payments\ByTypeAccrual;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class VIEW extends \Reports\reportView
{
    private $sumColumn;
    private $typeAccrual_Array;
    private $FullData;
    


    /**
     * @var \PhpOffice\PhpSpreadsheet\
     */

    private $headSettings;
    private $globalRow;
    private $start_TA = 7;


    function __construct()
    {
        $path = $_SERVER['DOCUMENT_ROOT'];
        $this->resultFilePath = $path."/ImpExp";
        $this->globalRow = 1;
        $this->spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $this->SheetResult = $this->spreadsheet->getActiveSheet();
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
        $this->typeAccrual_Array = $data_array['TA']['Data'];
        $this->sumColumn = $data_array['TA']['Columns'];
        $this->FullData = $data_array['Data'];
    }

    private function setValue($col,$row,$val)
    {
        $dstCell = Coordinate::stringFromColumnIndex($col) . (string)($row);
        $this->SheetResult->setCellValue($dstCell, $val);

    }



    private function setStyle($col,$row,$style)
    {
        $dstCell = Coordinate::stringFromColumnIndex($col) . (string)($row);
        $this->SheetResult->getStyle($dstCell)->getNumberFormat()->setFormatCode($style); //'$ #,##0.00'

    }


    private function widthColumn($column,$width,$autoSize = true)
    {
        $cells = Coordinate::stringFromColumnIndex($column);
        $this->SheetResult->getColumnDimension($cells)->setWidth($width);
        $this->SheetResult->getColumnDimension($cells)->setAutoSize($autoSize);
    }

    private function mergeCel($cStart,$rStart,$cEnd,$rEnd)
    {
        $range1 = Coordinate::stringFromColumnIndex($cStart) . (string)($rStart);
        $range2 = Coordinate::stringFromColumnIndex($cEnd) . (string)($rEnd);
        $this->SheetResult->mergeCells("$range1:$range2");
    }


    public function createReport()
    {

        $this->createHeadReport();
        $this->createBodyReport();

        $this->saveFile();
    }


    public function saveFile()
    {
        $fileName = "$this->resultFilePath/$this->nameReport";
        unlink ($fileName);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->spreadsheet, "Xlsx");
        $writer->save($fileName);
    }


    private function createHeadReport()
    {
        $gCol = 0;
        $columns = $gCol + $this->sumColumn + 7;
        $gRow = $this->globalRow;


        $range1 = Coordinate::stringFromColumnIndex(1) . (string)($gRow);
        $range2 = Coordinate::stringFromColumnIndex($columns) . (string)($gRow+5);
        $this->SheetResult->getStyle("$range1:$range2")->getAlignment()->setHorizontal('center');
        $this->SheetResult->getStyle("$range1:$range2")->getAlignment()->setVertical("center");

        $this->mergeCel(1,$gRow,$columns,$gRow);
        $this->setValue(1,$gRow,'Отчет о поступлении средств в пользу '.$this->headSettings['name_organization']);
        $gRow += 1;

        $this->mergeCel(1,$gRow,$columns,$gRow);
        $this->setValue(1,$gRow,"за ".$this->headSettings['full_name_month'].'г.');

        $gRow += 1;

        $this->mergeCel(1,$gRow,$columns,$gRow);
        $this->setValue(1,$gRow,"Поступления - {$this->headSettings['sel_organization']}, пачка(и)-({$this->headSettings['sel_packet']}), ЖЭУ - ({$this->headSettings['sel_JEU']})");

        $gRow += 2;

        $this->SheetResult->getRowDimension($gRow+1)->setRowHeight(153);

        //$this->SheetResult->getColumnDimensionByColumn('A')->setAutoSize(true);


        $this->widthColumn(1,20,false);
        $this->widthColumn(2,20,false);
        $this->widthColumn(3,7,false);
        $this->widthColumn(4,9,false);
        $this->widthColumn(5,36,false);
        $this->widthColumn(6,11,false);
        $this->mergeCel(1,$gRow,1,$gRow+1);
        $this->mergeCel(2,$gRow,2,$gRow+1);
        $this->mergeCel(3,$gRow,3,$gRow+1);
        $this->mergeCel(4,$gRow,4,$gRow+1);
        $this->mergeCel(5,$gRow,5,$gRow+1);
        $this->mergeCel(6,$gRow,6,$gRow+1);

        $head = Array(
            "id_LS" => "Лицевой счет",
            "name_JEU" => "Название ЖЭУ",
            "status_street" => "",
            "name_street" => "Улица",
            "house" => "Дом",
            "room" => "Квартира",
            "fam" => "Ф.",
            "im" => "И.",
            "ot" => "О.",
            "summa" => "Сумма"
        );
        $this->insertHeadRow($head,$gRow);

        $this->mergeCel($this->start_TA,$gRow,$this->sumColumn+$this->start_TA-1,$gRow);
        $this->setValue($this->start_TA,$gRow,'Детализация платежей по видам начислений');

        $col = 1;
        foreach ($this->typeAccrual_Array as $key => $valueArray){

            $this->setValue($this->start_TA+$col-1,$gRow+1,$valueArray['name_type_accrual']);

            $range1 = Coordinate::stringFromColumnIndex($this->start_TA) . (string)($gRow+1);
            $range2 = Coordinate::stringFromColumnIndex($this->start_TA+$col) . (string)($gRow+1);
            $this->SheetResult->getStyle("$range1:$range2")->getAlignment()->setTextRotation(90);
            $this->SheetResult->getStyle("$range1:$range2")->getAlignment()->setWrapText(true);
            $this->SheetResult->getStyle("$range1:$range2")->getAlignment()->setVertical("bottom");
            $col ++;
        }

        $this->globalRow +=4;
    }

    private function insertHeadRow($DataArray,$gRow)
    {
        /*
         * "id_LS" => "Лицевой счет",
        "name_JEU" => "Название ЖЭУ",
        "status_street" => "",
        "name_street" => "Улица",
        "house" => "Дом",
        "room" => "Квартира",
        "fam" => "Ф.",
        "im" => "И.",
        "ot" => "О."б
        summa
         */
        $this->setValue(1,$gRow,$DataArray['name_JEU']);
        $this->setValue(2,$gRow,$DataArray['status_street']." ".$this->mb_strtoupper_first($DataArray['name_street']));
        $this->setValue(3,$gRow,$DataArray['house']);
        $this->setValue(4,$gRow,$DataArray['room']);
        $this->setValue(5,$gRow,$this->mb_strtoupper_first($DataArray['fam'])." ".$this->mb_strtoupper_first($DataArray['im'])." ".$this->mb_strtoupper_first($DataArray['ot']));
        $this->setValue(6,$gRow,$DataArray['summa']);
    }


    public function createBodyReport()
    {
        $format = '# ### ##0.00;[Red]# ### ##0.00';
        //$format = '0.00';

        //$this->sumColumn = $data_array['columns'];

        $gCol = 0;

        $gRow = $this->globalRow;
        $gRow = 7;


        foreach ($this->FullData as $key => $row_data){
            $head = $row_data['Head'];
            $Accruals = $row_data['Accruals'];
            $this->insertHeadRow($head,$gRow);
            foreach ($Accruals as $id => $DA){
                if ($col = $this->typeAccrual_Array[$DA['id_type_accrual']]['col']){
                    $this->setValue($this->start_TA+$col-1,$gRow,$DA['summa']);
                    //$this->setValue($this->start_TA+$col-1,$gRow,"$col _ {$DA['id_type_accrual']}");
                }
            }
            $gRow++;
        }
        $this->mergeCel(1,$gRow,$this->start_TA-2,$gRow);
        $this->setValue(1,$gRow,"ИТОГО:");

        $dstCell = Coordinate::stringFromColumnIndex(1) . (string)($gRow);
        $this->SheetResult->getStyle($dstCell)->getFont()->setBold(true);

        $ColF = $this->start_TA-1;
        while ($ColF <= $this->sumColumn +  $this->start_TA-1){

            $this->setStyle($ColF,$gRow,'# ### ### ##0.00;-[Red]# ### ##0.00');

            $dstCell = Coordinate::stringFromColumnIndex($ColF) . (string)($gRow);
            $C = Coordinate::stringFromColumnIndex($ColF);
            $rows =  $gRow-1;
            $this->SheetResult->setCellValue($dstCell, "=SUM({$C}5:{$C}{$rows})");
            $this->SheetResult->getStyle($dstCell)->getFont()->setBold(true);

            $ColF += 1;
        }

/*
        $this->SheetResult->getColumnDimension(Coordinate::stringFromColumnIndex(1))->setAutoSize(true);
        for ($i= 2 ;$i <= $this->sumColumn + 7; $i++){
            //$this->SheetResult->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
            $this->SheetResult->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setWidth(13);

        }
*/
        $C = Coordinate::stringFromColumnIndex($this->sumColumn + 6);
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

        $this->SheetResult ->getStyle("A5:$C$gRow")->applyFromArray($styleArray);

    }

    private function mb_strtoupper_first($str, $encoding = 'UTF8')
    {
        $str = mb_strtolower($str, $encoding);
        return
            mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding) .
            mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
    }

}