<?php
namespace models;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use models\QRcode\QR_Level;
use models\QRcode\QR_TypeField;

class ReportOnPattern
{

    private $excelPatternPath;
    private $excelPatternName;

    private $resultFileName;
    private $resultFilePath;

    private $BoockPattern;
    /**
     * @var \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     */
    private $SheetPattern;


    

    private $widthReport;
    private $rowReport;
    private $rowReportRead,$nomCol;

    private $H;

    private $activTable;
    private $table;
    private $group;
    private $row;
    private $defaultRowHeight = 15;
    private $saveToPdf = false;
    private $saveToHTML = false;
    private $copyPatternToDestination = false;

    private $sizePixelForQrCode;
    private $QrCodeOffsetX = 0;
    private $QrCodeOffsetY = 0;

    private $range_printArea_RowStart = 1;
    private $printArea = false;
    private $defaultFontName = "Arial";
    private $defaultFontSize = 10;



    function __construct()
    {

        $this->excelPatternName = 'pattern.xlsx';
        $path = $_SERVER['DOCUMENT_ROOT'];
        $this->resultFilePath = $path."ImpExp";

        $this->H = Array();
        $this->table = Array();
        $this->rowReport = 1;
        $this->sizePixelForQrCode = 200;
    }
    /**
     *
     */
    public function run()
    {
        $this->defineSheetResult();
        $this->openFile();
        $this->copyHeadFile();
        $this->copyStyleXFCollection($this->BoockPattern,$this->spreadsheet );
        $this->detectWidthReport();
        $this->detectCommand();
        $this->MainProcessing();
        $this->deleteTechnicalColumns();
        $this->saveFile();
    }

    public function copyPatternToDestination()
    {
        $this->copyPatternToDestination = true;
        $file = "$this->excelPatternPath/$this->excelPatternName";
        $newfile = "$this->resultFilePath/{$this->resultFileName}_TMP.xlsx";
        copy($file, $newfile);
    }



    public function saveToPdf()
    {
        $this->saveToPdf = true;
    }


    public function saveToHTML()
    {
        $this->saveToHTML = true;
    }


    public function printArea_enabled()
    {
        $this->printArea = true;
    }


    /**
     * @param string $defaultFontName
     */
    public function setDefaultFontName(string $defaultFontName): void
    {
        $this->defaultFontName = $defaultFontName;
    }

    /**
     * @param int $defaultFontSize
     */
    public function setDefaultFontSize(int $defaultFontSize): void
    {
        $this->defaultFontSize = $defaultFontSize;
    }

    /**
     * @param mixed $QrCodeOffsetX
     */
    public function setQrCodeOffsetX($QrCodeOffsetX)
    {
        $this->QrCodeOffsetX = $QrCodeOffsetX;
    }

    /**
     * @param mixed $QrCodeOffsetY
     */
    public function setQrCodeOffsetY($QrCodeOffsetY)
    {
        $this->QrCodeOffsetY = $QrCodeOffsetY;
    }
    /**
     * @param mixed $sizePixelForQrCode
     */
    public function setSizePixelForQrCode($sizePixelForQrCode)
    {
        $this->sizePixelForQrCode = $sizePixelForQrCode;
    }

    /**
     * @param int $defaultRowHeight
     */
    public function setDefaultRowHeight(int $defaultRowHeight)
    {
        $this->defaultRowHeight = $defaultRowHeight;
    }
    /**
     * @param array $H
     */
    public function setH(array $H)
    {
        $this->H = $H;
    }

    /**
     * @param string $newVar
     * @param array $array
     */
    public function setArray(string $newVar,array $array)
    {
        $this->{$newVar} = Array();
        $this->{'t'.$newVar} = Array();
        $this->{'notEnd_'.$newVar} = true;
        $this->{'t'.$newVar} = $array;
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




    /**
     *
     */
    private function MainProcessing()
    {
        foreach ($this->table as $key => $newArray){
            if (is_array($newArray))
                $this->arrayProcessing($newArray);
        }
    }


    /**
     * @param $Array
     * @return bool
     */
    private function arrayProcessing($Array)
    {
        if ($this->checkArrayElements($Array)){
            if ($Array['type'] == 'Table'){
                $this->initStartReadFromTable($Array);
            }
            $this->blockProcessing($Array);
        }else{
            foreach ($Array as $key => $newArray){
                if (is_array($newArray))
                    $this->arrayProcessing($newArray);
            }
        }
        return true;
    }

    /**
     * @param $Array
     */
    private function blockProcessing($Array)
    {
        $row = $this->rowReport;

        switch ($Array['type']){
            case 'Table' :
                $this->prepareData($Array['nameBlock']);
                if ($this->notEndOfArray($Array['nameBlock'])) {
                    $this->copyBlockPattern($Array);
                    $this->rowReport = $this->rowReport + $Array['rows'];
                }
                break;
            case 'Group' :
                if ( $this->groupProcessing($Array) ){
                    $this->copyBlockPattern($Array);
                    $this->rowReport = $this->rowReport + $Array['rows'];
                }
                break;
            case 'Block' :
                if ($this->conditionProcessing($Array['if'])){
                    $this->copyBlockPattern($Array);
                    $this->rowReport = $this->rowReport + $Array['rows'];
                }
                break;
        }

        foreach ($Array as $key => $newArray){
            if (is_array($newArray))
                $this->arrayProcessing($newArray);
        }

        if (array_key_exists("rowsInTable",$Array)){
            if ($Array['rowsInTable'] != 0 ){
                $this->rowReport =  $this->rowReport + ((int)$Array['rowsInTable']-($this->rowReport-$row));
            }
        }

        if (array_key_exists("if",$Array) && $Array['type']=='Table'){
            if ($this->conditionProcessing($Array['if']) && $this->notEndOfArray($Array['nameBlock'])){
                $this->nextData($Array['nameBlock']);
                $this->blockProcessing($Array);
            }
        }
    }


    /**
     * @param $Array
     * @return bool
     */
    private function groupProcessing($Array)
    {
        $ret = false;
        $nameTable = $Array['if'];
        $nameBlock = $Array['nameBlock'];
        //$arr['t1']['groupName']['SUM']['name']='value';

        if ($this->{'tStart'.$nameTable} === true){
            $this->{'initGroup'.$nameTable.$nameBlock} = true;
        }


        if ($this->{'initGroup'.$nameTable.$nameBlock} === true){
            $this->initGroup($Array);
        }else{
            $this->fillingInGroupFields($Array);
        }

        if ($this->checkNextRecord($Array)){ // если следующая запись не равна текущей либо пустая
            $this->{'initGroup'.$nameTable.$nameBlock} = true;
            $ret = true;
        }

        if (!$this->notEndOfArray($nameTable)){
            $ret = true;
        }

        return $ret;
    }



    private function fillingInGroupFields($Array)
    {
        $nameTable = $Array['if'];
        $nameBlock = $Array['nameBlock'];
        $table = $this->{$nameTable};
        if (is_array($this->group[$nameTable][$nameBlock])===true) {
            foreach ($this->group[$nameTable][$nameBlock] as $aggregateFunction => $arrayVariable) {
                foreach ($arrayVariable as $variable => $value) {
                    if ($aggregateFunction == 'SUM') {
                        $value = (float)$value + (float)$table[$variable];
                        $this->group[$nameTable][$nameBlock][$aggregateFunction][$variable] = $value;
                    }
                    if ($aggregateFunction == 'MIN') {
                        if ($value > $table[$variable]) $value = $table[$variable];
                        $this->group[$nameTable][$nameBlock][$aggregateFunction][$variable] = $value;
                    }
                    if ($aggregateFunction == 'MAX') {
                        if ($value < $table[$variable]) $value = $table[$variable];
                        $this->group[$nameTable][$nameBlock][$aggregateFunction][$variable] = $value;
                    }
                }
            }
        }
    }


    private function checkNextRecord($Array)
    {
        $ret = False;
        $nameTable = $Array['if'];
        $nameBlock = $Array['nameBlock'];
        $dataThis = $this->{$nameTable};
        $dataNext = $this->{'next'.$nameTable};
        if ($dataNext === false){ // если следующая строка это конец массива
            $ret = true;
        }else{
            if ($nameBlock != 'SUMMTable')
                if ($dataNext[$nameBlock] != $dataThis[$nameBlock]){
                    $ret = true;
            }
        }

        return $ret;
    }


    /**
     * @param $Array
     */
    private function initGroup($Array)
    {
        $nameTable = $Array['if'];
        $nameBlock = $Array['nameBlock'];
        $rowData = $this->{$nameTable};

        for($row = $Array['row']; $row < $Array['row']+$Array['rows']; $row++){
            for($col = 1; $col<=$this->widthReport; $col++){
                $value = $this->getCellValue($col,$row);
                $aggregateFunction = mb_substr($value,1,3);
                $regexp = "/'[\w\s]+'/ui";
                preg_match($regexp,$value,$arr);
                $variable = str_replace("'","",$arr[0]);
                $variable = str_replace('"',"",$variable);
                switch ($aggregateFunction){
                    case 'SUM':
                    case 'MIN':
                    case 'MAX':
                        $this->group[$nameTable][$Array['nameBlock']][$aggregateFunction][$variable]=$rowData[$variable];
                        break;
                }
            }
        }
        $this->{'initGroup'.$nameTable.$nameBlock} = false;
    }

    /**
     * @param $Array
     */
    private function copyBlockPattern($Array)
    {
        $sheet = $this->SheetPattern;
        $srcRowStart = $Array['row'];
        $srcRowEnd = $Array['row'] + $Array['rows'] - 1;

        $srcColumnEnd = $this->widthReport;
        $destRowStart = $this->rowReport;
        $destSheet = $this->SheetResult;

        if ($Array['type'] == 'Group'){
            $nameTable = $Array['if'];
            $nameBlock = $Array['nameBlock'];
            if (is_array($this->group[$nameTable][$nameBlock])===true){
                foreach ($this->group[$nameTable][$nameBlock] as $aggregateFunction => $arrayVariable) {
                    foreach ($arrayVariable as $variable => $value){
                     //   print "\$this->\{$aggregateFunction.'_'.$nameTable\}[$variable] = $value </br>";
                        ${$aggregateFunction.'_'.$nameTable}[$variable] = $value;

                    }
                }
            }
        }
        if ($this->activTable != null)
            if (strlen($this->activTable) != 0)
                ${$this->activTable} = $this->{$this->activTable};

        $srcColumnStart = 1;
        $destColumnStart = 1;

        $rowCount = 0;
        for ($row = $srcRowStart; $row <= $srcRowEnd; $row++) {
            $colCount = 0;

            for ($col = $srcColumnStart; $col <= $srcColumnEnd; $col++) {
                $cell = $sheet->getCellByColumnAndRow($col, $row);
                $style = $sheet->getStyleByColumnAndRow ($col, $row);
                $val='';
                $dstCell = Coordinate::stringFromColumnIndex($destColumnStart + $colCount) . (string)($destRowStart + $rowCount);
                $val1 = $cell->getValue();

                $typeData = 'All';
                if (strpos( (string)$val1, '$') === false){
                    $val1 = "'".$val1."'";
                }else{
                    if (substr($val1,1,5)=='DATE_'){
                        $val1 = str_replace("DATE_","",$val1);
                        $typeData = 'Date';
                    }
                    if (substr($val1,1,7)=='QrCODE_'){
                        $val1 = str_replace("QrCODE_","",$val1);
                        $typeData = 'QrCODE';
                    }

                    if (substr($val1,1,5)=='SHEET'){
                        //
                        $this->SheetResult->getPageSetup()->addPrintAreaByColumnAndRow(1,$this->range_printArea_RowStart,$destColumnStart + $colCount,($destRowStart + $rowCount)-1,0);
                        $this->range_printArea_RowStart = ($destRowStart + $rowCount+1) ;
                        $typeData = 'SHEET';
                    }

                }
                $command = '$val = '.$val1.';';
                /* *************************************************************************/
                /* ↓↓↓↓↓↓↓↓↓↓↓↓↓↓*/
                eval($command);
                /* ↑↑↑↑↑↑↑↑↑↑↑↑↑↑*/
                /* *************************************************************************/

                if ($typeData == 'Date'){
                    $month = (int) date('m',strtotime($val));
                    $day = (int) date('d',strtotime($val));
                    $year = (int) date('Y',strtotime($val));
                    $time = gmmktime(0,0,0,$month,$day,$year);
                    $destSheet->getCell($dstCell)->setValue(\PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($time));
                    $destSheet->getStyle($dstCell)
                     ->getNumberFormat()
                        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYYPOINT);
                }

                if ($typeData == 'QrCODE'){
                    $this->addQrCode($dstCell,$val);
                }

                if ($typeData == 'All'){
                    if (! is_array($val))
                        $destSheet->setCellValue($dstCell, $val);
                }


                $this->duplicateStyle($style, $dstCell);

                $colCount++;
            }

            $h = $sheet->getRowDimension($row)->getRowHeight();
            $destSheet->getRowDimension($destRowStart + $rowCount)->setRowHeight($h);

            $rowCount++;
        }

        if ($this->copyPatternToDestination === false) {
            foreach ($sheet->getMergeCells() as $mergeCell) {
                $mc = explode(":", $mergeCell);
                $mergeColSrcStart = Coordinate::columnIndexFromString(preg_replace("/[0-9]*/", "", $mc[0]));
                $mergeColSrcEnd = Coordinate::columnIndexFromString(preg_replace("/[0-9]*/", "", $mc[1]));
                $mergeRowSrcStart = ((int)preg_replace("/[A-Z]*/", "", $mc[0]));
                $mergeRowSrcEnd = ((int)preg_replace("/[A-Z]*/", "", $mc[1]));

                $relativeColStart = $mergeColSrcStart - $srcColumnStart;
                $relativeColEnd = $mergeColSrcEnd - $srcColumnStart;
                $relativeRowStart = $mergeRowSrcStart - $srcRowStart;
                $relativeRowEnd = $mergeRowSrcEnd - $srcRowStart;

                if (0 <= $mergeRowSrcStart && $mergeRowSrcStart >= $srcRowStart && $mergeRowSrcEnd <= $srcRowEnd) {
                    $targetColStart = Coordinate::stringFromColumnIndex($destColumnStart + $relativeColStart);
                    $targetColEnd = Coordinate::stringFromColumnIndex($destColumnStart + $relativeColEnd);
                    $targetRowStart = $destRowStart + $relativeRowStart;
                    $targetRowEnd = $destRowStart + $relativeRowEnd;

                    $merge = (string)$targetColStart . (string)($targetRowStart) . ":" . (string)$targetColEnd . (string)($targetRowEnd);
                    //Merge target cells
                    $destSheet->mergeCells($merge);
                }
            }

        }
    }

    /**
     * @param $Array
     */
    private function initStartReadFromTable($Array)
    {
        $this->{'tStart'.$Array['nameBlock']} = true;
    }


    /**
     * @param $nameArray
     */
    private function prepareData($nameArray)
    {
        //\models\ErrorLog::saveError(current($this->tt1),typeSaveMode: "w+");
        $this->activTable = $nameArray;
        $this->{$nameArray} = current((Array)$this->{'t'.$nameArray});
        $this->{'next'.$nameArray} = next($this->{'t'.$nameArray});
        prev($this->{'t'.$nameArray});

    }

    /**
     * @param $nameArray
     */
    private function nextData($nameArray)
    {
        next($this->{'t'.$nameArray});
        $this->{'notEnd_'.$nameArray} = key($this->{'t'.$nameArray}) !== Null ? true : false;
        $this->{'tStart'.$nameArray} = false;
    }

    /**
     * @param $nameArray
     * @return mixed
     */
    private function notEndOfArray($nameArray)
    {
        return $this->{'notEnd_'.$nameArray};
    }

    /**
     * @param $condition
     * @return bool
     */
    private function conditionProcessing($condition)
    {
        $ret = true;
        $command = '$ret = '.$condition.' ? true : false ;';
        eval($command);
        return $ret;
    }

    /**
     * @param $array
     * @return bool
     */
    private function checkArrayElements($array)
    {
        $res = true;
        if (!array_key_exists("nameBlock",$array)) $res = false;
        if (!array_key_exists("type",$array)) {
            $res = false;
        }else{
            if ($array['type']=='Table')
                if (!array_key_exists("rowsInTable",$array)){
                    $res = false;
                }
        }
        if (!array_key_exists("row",$array)) $res = false;
        if (!array_key_exists("rows",$array)) $res = false;
        if (!array_key_exists("if",$array)) $res = false;

        return $res;
    }


    /**
     * @return bool
     */
    private function detectCommand()
    {
        $this->rowReportRead = 1;
        $this->nomCol = 1;
        $arrayNomCol = Array(
            1=>$this->widthReport+2,
            2=>$this->widthReport+2+4,
            3=>$this->widthReport+2+7,
            4=>$this->widthReport+2+10,
            );
        $str = '{';
        $exit = false;
        while (!$exit){
                $cell = $this->getCellValue($arrayNomCol[$this->nomCol],$this->rowReportRead);
                if ($cell!=null){
                    $nameNew = $this->getCellValue($arrayNomCol[$this->nomCol]+1,$this->rowReportRead);
                    $rows = $this->getCellValue($arrayNomCol[$this->nomCol]+2,$this->rowReportRead);
                    $if = $this->getCellValue($arrayNomCol[$this->nomCol]+3,$this->rowReportRead);
                    switch ($cell) {
                        case "Table":
                        case "Group":
                        case "Block":
                            $table = Array();
                            $str1 = '';
                            $str1 = $str1.'"nameBlock":"'.$nameNew.'",';
                            $str1 = $str1.'"type":"'.$cell.'",';
                            $str1 = $str1.'"row":'.$this->rowReportRead.',';
                            $str1 = $str1.'"rows":'.$rows.',';
                            $str1 = $str1.'"if":"'.$if.'",';
                            $str1 = '"'.$nameNew.'":{'.$str1;
                            $str = $str.$str1;
                            break;
                    }

                    switch ($cell) {
                        case "EndTable":
                        case "EndGroup":
                        case "EndBlock":
                            $rowsInTable = $this->getCellValue($arrayNomCol[$this->nomCol]+2,$this->rowReportRead);
                            $str1 = '';
                            if ($cell=='EndTable'){
                                $str1 = $str1.'"rowsInTable":"'.$rowsInTable.'",';
                            }
                            $str = $str.$str1;
                            if (substr($str,-1)==','){
                                $str = substr($str,0,strlen($str)-1);
                            }
                            $str = $str.'},';
                            break;
                    }
                }
                $this->nextCol();
                if ($this->getCellValue($this->widthReport+2,$this->rowReportRead)=='EndReport') $exit = true;
                if ($this->rowReportRead>100) $exit = true;
            }
        if (substr($str,-1)==','){
            $str = substr($str,0,strlen($str)-1);
        }

        $str = $str.'}';
        $this->table = json_decode($str,true);

        if ( is_Array($this->table)){
            return true;
        }else {
            return false;
        }
    }


    /**
     *  Смещение колонки Для чениея из шаблона в массив
     *  для detectCommand()
     */
    private function nextCol()
    {
        $this->nomCol = $this->nomCol + 1;
        if ($this->nomCol>4) {
            $this->rowReportRead = $this->rowReportRead + 1;
            $this->nomCol = 1;
        }
    }


    /**
     *
     */
    public function defineSheetResult()
    {
        if ($this->copyPatternToDestination === false){
            $this->spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $this->spreadsheet->getDefaultStyle()->getFont()->setName($this->defaultFontName)->setSize($this->defaultFontSize);
            $this->SheetResult = $this->spreadsheet->getActiveSheet();
            $this->SheetResult->getDefaultRowDimension()->setRowHeight($this->defaultRowHeight);
            //$this->SheetResult->getStyle()

        }else{
            $BoockPattern = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
            $BoockPattern->setReadDataOnly(false);
            $loadFile = "$this->resultFilePath/{$this->resultFileName}_TMP.xlsx";
            $this->spreadsheet = $BoockPattern->load($loadFile);
            $this->spreadsheet->setActiveSheetIndex(0);
            $this->SheetResult = $this->spreadsheet->getActiveSheet();
            //$this->SheetResult->getDrawingCollection();
        }
    }

    /**
     * Global images index table.
     *
     * @var array
     */
    private static $imageIndexTable = array();

    /**
     * Change the current image index value based on the supplied file path and name.
     */
    public function setMergeDrawings($pValue)
    {
        $i = array_search($pValue, self::$imageIndexTable);
        if ($i === false) {
            self::$imageIndexTable[$this->imageIndex] = $pValue;
        } else {
            $this->imageIndex = $i;
        }
    }

    /**
     *
     */
    public function openFile()
    {
        $BoockPattern = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $BoockPattern->setReadDataOnly(false);
        $loadFile = "$this->excelPatternPath/$this->excelPatternName";
        $this->BoockPattern = $BoockPattern->load($loadFile);
        $this->BoockPattern->setActiveSheetIndex(0);
        $this->SheetPattern = $this->BoockPattern->getActiveSheet();
        //print_r($this->SheetPattern->getStyle("A2")->getFont()->setSize(11));
        //print "</br>";
    }




    public function saveFile()
    {

        $fileName = "$this->resultFilePath/$this->resultFileName.xlsx";
        unlink($fileName);

        if ($this->saveToHTML){
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->spreadsheet, "Html");
            $writer->save($fileName);
        }else{
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->spreadsheet, "Xlsx");
            $writer->save($fileName);

        }

        if ($this->saveToPdf){
            setlocale(LC_MONETARY, 'ru_RU');
            $cmd = "/usr/bin/libreoffice --headless --convert-to pdf:writer_pdf_Export --outdir $this->resultFilePath $fileName";
            ///usr/bin/libreoffice --headless --convert-to pdf:writer_pdf_Export --outdir /var/www/html/ImpExp 07BD3DD0-8A64-404B-ABDB-E84BD4FF22D8.xlsx
            shell_exec($cmd);
            //soffice --headless --convert-to html Математика
            //Конвертирует все поддерживаемые файлы в каталоге Математика в html формат и сохранит их в текущем каталоге
            unlink($fileName);
        }

        $newfile = "$this->resultFilePath/{$this->resultFileName}_TMP.xlsx";
        unlink($newfile);
    }


    private function copyHeadFile()
    {
        if ($this->copyPatternToDestination === false) {
            $this->SheetResult->getPageSetup()->setFitToPage($this->SheetPattern->getPageSetup()->getFitToPage());
            $this->SheetResult->getPageSetup()->setScale($this->SheetPattern->getPageSetup()->getScale());
            $this->SheetResult->getPageSetup()->setFitToWidth($this->SheetPattern->getPageSetup()->getFitToWidth());
            $this->SheetResult->getPageSetup()->setFitToHeight($this->SheetPattern->getPageSetup()->getFitToHeight());
            $this->SheetResult->getPageSetup()->setOrientation($this->SheetPattern->getPageSetup()->getOrientation());
            $this->SheetResult->getPageSetup()->setHorizontalCentered($this->SheetPattern->getPageSetup()->getHorizontalCentered());
            $this->SheetResult->getPageSetup()->setVerticalCentered($this->SheetPattern->getPageSetup()->getVerticalCentered());



            $this->SheetResult->getPageMargins()->setTop($this->SheetPattern->getPageMargins()->getTop());
            $this->SheetResult->getPageMargins()->setBottom($this->SheetPattern->getPageMargins()->getBottom());
            $this->SheetResult->getPageMargins()->setFooter($this->SheetPattern->getPageMargins()->getFooter());
            $this->SheetResult->getPageMargins()->setHeader($this->SheetPattern->getPageMargins()->getHeader());
            $this->SheetResult->getPageMargins()->setLeft($this->SheetPattern->getPageMargins()->getLeft());
            $this->SheetResult->getPageMargins()->setRight($this->SheetPattern->getPageMargins()->getRight());
        }
    }

    /**
     *
     */
    private function detectWidthReport()
    {

        $col = 1;
        while (($col<1024)&&($this->getCellValue($col,1)<>"Width")){
            $this->SheetResult->getColumnDimension($this->IntColumnToStr($col))->setWidth(
                $this->SheetPattern->getColumnDimension($this->IntColumnToStr($col))->getWidth());
            $col  = $col + 1;
        }
        $this->widthReport = $col-1;
    }


    /**
     * @param $cellOrCol
     * @param null $row
     * @return mixed
     */
    private function getCellValue($cellOrCol, $row = null)
    {
        //column set by index
        if(is_numeric($cellOrCol)) {
            $cell = $this->SheetPattern->getCellByColumnAndRow($cellOrCol, $row);
        } else {
            $lastChar = substr($cellOrCol, -1, 1);
            if(!is_numeric($lastChar)) { //column contains only letter, e.g. "A"
                $cellOrCol .= $row;
            }

            $cell = $this->SheetPattern->getCell($cellOrCol);
        }
        $val = $cell->getValue();
        return $val;
    }


    /**
     * @param $cellOrCol
     * @param null $row
     * @param string $value
     *
     */
    private function setCellValue($cellOrCol, $row = null,$value = '')
    {
        //column set by index
        if(is_numeric($cellOrCol)) {
            $cell = $this->SheetResult->getCellByColumnAndRow($cellOrCol, $row);
        } else {
            $lastChar = substr($cellOrCol, -1, 1);
            if(!is_numeric($lastChar)) { //column contains only letter, e.g. "A"
                $cellOrCol .= $row;
            }

            $cell = $this->SheetResult->getCell($cellOrCol);
        }
        $cell->setValue($value);
    }

    /**
     * @param $columnInt
     * @return mixed|string
     */
    private function IntColumnToStr($columnInt)
    {
        $char = Array(0=>'',1=>'A',2=>'B',3=>'C',4=>'D',5=>'E',6=>'F',7=>'G',8=>'H',9=>'I',10=>'J',11=>'K',12=>'L',13=>'M',14=>'N',15=>'O',16=>'P',17=>'Q',18=>'R',19=>'S',20=>'T',21=>'U',22=>'V',23=>'W',24=>'X',25=>'Y',26=>'Z');
        if ($columnInt<=26){
            $res = $char[$columnInt];
        }else{
            $del = $columnInt/26;
            $del =  intval (floor($del));
            $ostDel = ($columnInt%26);
            $res = $char[$del].$char[$ostDel];
        }

        return $res;
    }

    /**
     * @param Worksheet $sheet
     * @param $srcRowStart
     * @param $srcRowEnd
     * @param $width
     * @param $destRowStart
     * @param Worksheet|null $destSheet
     */
    public function copyRows( Worksheet $sheet, $srcRowStart, $srcRowEnd,$width,$destRowStart, Worksheet $destSheet = null) {


    }

    private function duplicateStyle($style, $dstCell)
    {
        if ($this->copyPatternToDestination === false) {
            $this->SheetResult->duplicateStyle($style, $dstCell);
        }
    }
    /**
     * @param Spreadsheet $sourceSheet
     * @param Spreadsheet $destSheet
     */
    public function copyStyleXFCollection(Spreadsheet $sourceSheet, Spreadsheet $destSheet)
    {
        if ($this->copyPatternToDestination === false) {
            $collection = $sourceSheet->getCellXfCollection();
            foreach ($collection as $key => $item) {
                $destSheet->addCellXf($item);
            }
        }
    }

    private function deleteTechnicalColumns()
    {
        if ($this->copyPatternToDestination === true) {
            for ($Col = $this->widthReport + 1;$Col <= $this->widthReport + 10;$Col++){
                $this->SheetResult->removeColumnByIndex($Col);
            }
        }
    }

    private function addQrCode($cells,$data)
    {
        $Obj_QR = new \models\QRcode\QR;

        $Obj_QR->QR($data,QR_TypeField::String,QR_Level::QRLevel_M,2);
        $Obj_QR->setSizeInPixels($this->sizePixelForQrCode);
        $gdImage = $Obj_QR->getQRCodeImage();

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing(); // Новый экземпляр "Рисоваки")
        $drawing->setCoordinates($cells); // Координаты картинки
        $gdImage = imagecreatefromstring($gdImage);

        $drawing->setImageResource($gdImage);

        $drawing->setRenderingFunction(
            \PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing::RENDERING_JPEG
        );
        $drawing->setMimeType(\PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing::MIMETYPE_JPEG);
        $drawing->setOffsetX($this->QrCodeOffsetX);
        $drawing->setOffsetY($this->QrCodeOffsetY);
        $drawing->setWorksheet($this->SheetResult); // Нужная вкладка
    }
}
