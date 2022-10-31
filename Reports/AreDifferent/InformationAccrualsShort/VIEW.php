<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */


namespace Reports\AreDifferent\InformationAccrualsShort;
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
        $this->gRow = 1;
        $this->openPatternFile();
        $this->defineSheetResult();
        $this->copyHeadFile();

        $this->createHeadTable();
        $this->gRow++;

        foreach ($this->data as $id_LS => $data){
            $this->insertRow($data);
            $this->gRow++;
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

    private function insertValue($column,$value)
    {
        $cells = Coordinate::stringFromColumnIndex($column).(string)($this->gRow);
        $this->SheetResult->setCellValue($cells, $value);

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
        $col = 1;

        $this->insertValue($col,'Л/С');
        $this->widthColumn($col,30);
        $col ++;

        $this->insertValue($col,'Улица');
        $this->widthColumn($col,30);
        $col ++;

        $this->insertValue($col,'Дом');
        $this->widthColumn($col,30);
        $col ++;

        $this->insertValue($col,'Квартира');
        $this->widthColumn($col,30);
        $col ++;
/*
        $this->insertValue($col,'Комнат');
        $this->widthColumn($col,30);
        $col ++;
*/
        $this->insertValue($col,'Площадь');
        $this->widthColumn($col,30);
        $col ++;

        $this->insertValue($col,'Прописано');
        $this->widthColumn($col,30);
        $col ++;

        $this->insertValue($col,'Владельцев');
        $this->widthColumn($col,30);
        $col ++;

        $this->insertValue($col,'По акту');
        $this->widthColumn($col,30);
        $col ++;

        $this->insertValue($col,'Соц. норма');
        $this->widthColumn($col,30);
        $col ++;

        $this->insertValue($col,'Временно');
        $this->widthColumn($col,30);
        $col ++;

        $this->insertValue($col,'Фамилия');
        $this->widthColumn($col,30);
        $col ++;

        $this->insertValue($col,'Имя');
        $this->widthColumn($col,30);
        $col ++;

        $this->insertValue($col,'Отчество');
        $this->widthColumn($col,30);
        $col ++;


        $this->insertValue($col,'Сальдо на начало');
        $this->widthColumn($col,30);
        $col ++;

        while ($res = $this->typeAccrual->fetch() ){
            $this->insertValue($col,$res['name']);
            $this->widthColumn($col,30);
            $col ++;
        }

        $this->insertValue($col,'Оплата');
        $this->widthColumn($col,30);
        $col ++;

        $this->insertValue($col,'Сальдо на конец');
        $this->widthColumn($col,30);

    }

    private function insertRow($dataArray = Array())
    {
        $col = 1;

        $this->insertValue($col,$dataArray['id_LS']);
        $this->widthColumn($col,30);
        $col ++;

//        $this->insertValue($col,'Улица');
        $this->insertValue($col,$dataArray['name_street']);
        $this->widthColumn($col,30);
        $col ++;

//        $this->insertValue($col,'Дом');
        $this->insertValue($col,$dataArray['house']);
        $this->widthColumn($col,30);
        $col ++;

//        $this->insertValue($col,'Квартира');
        $this->insertValue($col,$dataArray['room']);
        $this->widthColumn($col,30);
        $col ++;
/*
        $this->insertValue($col,'комнат');
        $this->insertValue($col,$dataArray['rooms']);
        $this->widthColumn($col,30);
        $col ++;
*/
//        $this->insertValue($col,'Площадь');
        $this->insertValue($col,$dataArray['area']);
        $this->widthColumn($col,30);
        $col ++;

//        $this->insertValue($col,'Прописано');
        $this->insertValue($col,$dataArray['person']);
        $this->widthColumn($col,30);
        $col ++;

//        $this->insertValue($col,'Владельцев');
        $this->insertValue($col,$dataArray['person_owner']);
        $this->widthColumn($col,30);
        $col ++;

//        $this->insertValue($col,'По акту');
        $this->insertValue($col,$dataArray['person_act']);
        $this->widthColumn($col,30);
        $col ++;

 //       $this->insertValue($col,'Соц. норма');
        $this->insertValue($col,$dataArray['soc_norm']);
        $this->widthColumn($col,30);
        $col ++;

 //       $this->insertValue($col,'Временно');
        $this->insertValue($col,$dataArray['person_tmp']);
        $this->widthColumn($col,30);
        $col ++;

 //       $this->insertValue($col,'Фамилия');
        $this->insertValue($col,$dataArray['fam']);
        $this->widthColumn($col,30);
        $col ++;

//        $this->insertValue($col,'Имя');
        $this->insertValue($col,$dataArray['im']);
        $this->widthColumn($col,30);
        $col ++;

//        $this->insertValue($col,'Отчество');
        $this->insertValue($col,$dataArray['ot']);
        $this->widthColumn($col,30);
        $col ++;

//        $this->insertValue($col,$dataArray['saldo_start']);
        $this->widthColumn($col,30);
        $col ++;

        $table = $dataArray['table'];
        foreach ($table as $key => $value){
            $this->insertValue($col,$value['summa']);
            $this->widthColumn($col,30);
            $col ++;
        }

//        $this->insertValue($col,'Оплата');
        $this->insertValue($col,$dataArray['payment']);
        $this->widthColumn($col,30);
        $col ++;

//        $this->insertValue($col,'Сальдо на конец');
        $this->insertValue($col,$dataArray['saldo_end']);
        $this->widthColumn($col,30);

    }
    private function widthColumn($column,$width)
    {
        $cells = Coordinate::stringFromColumnIndex($column);
        //$this->SheetResult->getColumnDimension($cells)->setWidth($width);
        $this->SheetResult->getColumnDimension($cells)->setAutoSize(true);
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