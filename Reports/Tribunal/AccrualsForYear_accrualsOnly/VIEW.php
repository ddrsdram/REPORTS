<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */


namespace Reports\Tribunal\AccrualsForYear_accrualsOnly;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;


class VIEW extends \Reports\reportView
{

    /**
     * @var \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     */

    private $SheetPattern;

    private $headSettings;
    private $headSettings2;

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
    private $gCol;
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
        $this->headSettings2 = $this->headSettings;
        $this->headSettings =  current($this->data);

        $this->gRow = 0;
        $this->createHeadReport();



        $this->gRow = 3;
        $this->createHeadTable();
        $this->gRow += 1;

        foreach ($this->data as $id_LS => $data){
            $this->insertRow($data);
            $this->gRow += 1;
        }
        $this->insertValue(1,1,"ИТОГО:");
        $cells = Coordinate::stringFromColumnIndex(3).(string)($this->gRow+1 );
        $r = $this->gRow + 1;
        $this->SheetResult->getStyle($cells)->getAlignment()->setHorizontal("left");

        $SUMRANGE = 'c5:c'.$this->gRow;
        $this->SheetResult->setCellValue($cells , "=SUM($SUMRANGE)");

        $this->mergeCel(1,1,2,1);
        $this->mergeCel(3,1,$this->gCol,1);
        $this->gRow += 1;
        $this->setBorder(1,4,$this->gCol,$this->gRow);



        $this->bottomReport();
       // $this->SheetResult->freezePane('C9');
        $this->saveFile();
    }

    public function bottomReport()
    {
        $bookkeeper_KVPL = $this->headSettings2['bookkeeper_KVPL'];
        $post = $this->headSettings2['AccrualsForYear'];
        $this->insertValue(1,3,"$post  ________________________________ $bookkeeper_KVPL");
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

        $this->SheetResult->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
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
        $DA['accruals'] = 'Начисление';
//        $DA[''] = '';
        while ($res = $this->typeAccrual->fetch()){
            $arr = Array();
            $arr['name'] = $res['name'];
            $arr['detailing_general_report'] = $res['detailing_general_report'];
            $arr['summa1'] = 'Начислено';
            $DA['table'][] = $arr;
        }
        $this->insertRow($DA,true);
    }

    private function insertRow($DA = Array(),$head = false)
    {

        $col = 1;

        if ($head) {
            $this->widthColumn($col, 13.14, false);
        }

        $this->insertValue($col,1,$DA['name_month']);
        $col ++;

        if ($head){
            $this->widthColumn($col,11,false);
            $this->insertValue($col,1,'Основные итоги');
            $this->mergeCel($col,1,$col +1,1);
            $col ++;
            $col ++;
            $this->heightRow($this->gRow+1,94.5);
        }else{
            $this->insertValue($col,1,"Начисление");
            $col ++;
            $colTotal = $col;
            $col ++;
        }


        $DT = $DA['table'];

        $row = 1;

        foreach ($DT as $key => $DR){
            $columns = $DR['detailing_general_report'];

            if ($head){
                $this->mergeCel($col,1,$col + ($columns-1),1);
                $this->insertValue($col,$row,$DR['name']);

            }else{
                if ($columns == 1){
                    $this->insertValue($col,$row,$DR['summa'] + $DR['recalculate']);
                }else{
                    $this->insertValue($col,$row,$DR['summa1'] + $DR['recalculate']);

                }
                if ($columns>1){
                    $this->insertValue($col+1,$row,$DR['summa2']);
                }
                if ($columns>2){
                    $this->insertValue($col+2,$row,$DR['summa3']);
                }
            }

            $this->widthColumn($col,10);
            if ($columns>1)
                $this->widthColumn($col+1,10);
            if ($columns>2)
                $this->widthColumn($col+2,10);

            $col += $columns;
            if ($head) {
                $this->widthColumn($col, 10);
                $this->insertValue($col,1,"Справочно");
            }
        }

        if (!$head){

            $C1 = Coordinate::stringFromColumnIndex($col);
            $R1 = $this->gRow+1;
            $SUMRANGE = "D{$R1}:{$C1}{$R1}";
            $this->SheetResult->setCellValue("C{$R1}" , "=SUM($SUMRANGE)");
        }

        if (array_key_exists('pay',$DA)){
            $DP = $DA['pay'];
            $rowPay = 0;
            foreach ($DP as $key0 => $item){
                $rowPay ++;
                $col1 = 1;
                $this->insertValue($col1,1 + $rowPay ,$item['full_name_month']);
                $col1 ++;
                $this->insertValue($col1,1 + $rowPay ,"Оплата");
                $col1 ++;
                $this->insertValue($col1,1 + $rowPay ,$item['total_Summ']);
                $cells = Coordinate::stringFromColumnIndex($col1).(string)($this->gRow + 1 + $rowPay);
                $this->SheetResult->getStyle($cells)->applyFromArray(
                            [
                                'numberFormat' => [
                                                'formatCode' => '# ##0.00;# ##0.00'
                                                ]
                            ]
                    );

                $summa = round($item['summa'],2);
                $summa_ACT = round($item['summa_ACT'],2);
                $data_pay = date("d.m.Y",strtotime($item['data_pay']));
                $number_act = $item['number_act'];
                $date_act = date("d.m.Y",strtotime($item['date_act']));
                $text = "";
                if ($summa_ACT != 0)
                    $text = "$summa от $data_pay. ($summa_ACT в счет исп.док.№$number_act от $date_act)";
                $this->insertValue($col,1 + $rowPay ,$text);
                //$this->setBorder(1,1 + $rowPay,3,1 + $rowPay);


            }
            $r = $this->gRow + 1;
            $this->SheetResult->getStyle("A$r")->getAlignment()->setVertical("top");
            $this->mergeCel(1,1,1,1 + $rowPay);
            $this->gRow = $this->gRow + $rowPay;
        }

        if ($head){
            $C = Coordinate::stringFromColumnIndex($col-1);

            $this->SheetResult->getStyle("A4:BZ4")->getFont()->setSize(8)->setBold(1);
            $this->SheetResult->getStyle("A4:BZ4")->getAlignment()->setVertical("center");

            $this->SheetResult->getStyle("A4:BZ4")->getAlignment()->setHorizontal("center");

            $this->SheetResult->getStyle("D4:{$C}4")->getAlignment()->setTextRotation(90);
            $this->SheetResult->getStyle("D4:{$C}4")->getAlignment()->setWrapText(true);
            $this->SheetResult->getStyle("D4:{$C}4")->getAlignment()->setVertical("bottom");
        }
        $this->gCol = $col;
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
        $range1 = Coordinate::stringFromColumnIndex($cStart) . (string)($rStart);
        $range2 = Coordinate::stringFromColumnIndex($cEnd) . (string)($rEnd);
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