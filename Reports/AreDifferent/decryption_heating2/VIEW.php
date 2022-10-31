<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */


namespace Reports\AreDifferent\decryption_heating2;
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
    private $gRow;


    private $dataArrayST;
    private $dataArrayDEV;
    private $dataArrayOTH;
    private $countST;
    private $countDEV;
    private $countOTH;
    private $DH;
    
    private $standatrdsRow;
    private $devicesRow;
    private $standardsTarifRows = Array();
    
    function __construct()
    {
        $path = $_SERVER['DOCUMENT_ROOT'];
        $this->resultFilePath = $path."/ImpExp";
        $this->excelPatternName = 'pattern.xlsx';
    }

    public function run()
    {
        $this->gRow = 36;
        $this->defineSheetResult();
        $this->insertData();
        $this->footerReport();
        $this->saveFile();
    }

    private function insertData()
    {

        $this->insertValue(4,2,"За {$this->DH['name_month']}");
        $this->insertValue(5,2,$this->DH['name_organization']);
        $rowUP = $this->gRow;

        $R = $this->gRow+1;
        $f = "=(L){$R}";
        $this->insertRows($this->dataArrayST,"1.1","по нормат.потр.по строит.V домов, в т.ч.");


        if ($this->countDEV != 0){
            $R = $this->gRow+1;
            $f .= "+(L){$R}";
            $this->insertRows($this->dataArrayDEV,"1.2","по ОДПУи ИПУ",1);
        }

        if ($this->countOTH != 0) {
            $R = $this->gRow + 1;
            $f .= "+(L){$R}";
            $this->insertRows($this->dataArrayOTH, "1.3", "Хозяйственные постройки по другой площади  ");
        }

        $rowUP = $this->gRow + 1;
        // формирование итоговой строки по всему отчету
        $this->insertValue($rowUP,2,'1');
        $this->insertValue($rowUP,3,$this->DH['name_organization']);

        for ($i = 4; $i<=$this->columnsPattern ; $i++){
            $L = Coordinate::stringFromColumnIndex($i);
            $formula = str_replace("(L)",$L,$f);
            $this->insertValue($rowUP,$i ,$formula);
        }
    }
    
    public function insertRows($dataArray,$nameCol_2,$nameCol_3,$dev = 0)
    {
        $this->standatrdsRow = $this->gRow;
        $this->gRow ++ ;
        $id_tarif = 0 ;
        $standardsTarifRows = Array();
        foreach ($dataArray as $key => $DA){
            if ($id_tarif != $DA['id_tarif']){

                $standardsTarifRows[] = Array("row"=>$this->gRow,"name"=>$DA['name_tarif']);

                $this->gRow ++;
                $this->gRow ++;
                $id_tarif = $DA['id_tarif'];

            }
            $this->insertRow($DA,$dev);
            $this->gRow ++;
        }
        $standardsTarifRows[] =  Array("row"=>$this->gRow,"name"=>"");

        $this->totalBig($standardsTarifRows,$nameCol_2,$nameCol_3);
    }

    private function totalBig($standardsTarifRows,$nameCol_2,$nameCol_3)
    {
        $f = "=";
        $rowUP = 0;
        foreach ($standardsTarifRows as $key => $val){
            if (array_key_exists($key+1,$standardsTarifRows)){
                $value = $val['row'];
                $name = $val['name'];
                $rowDOWN = $standardsTarifRows[$key+1]['row'] - 1;
                $rowUP = $value + 2;
                $this->totalRow($rowUP,$rowDOWN,$name);
                $rUP = $rowUP - 1;
                $f .= "(L)$rUP+";
            }
        }
        $f = substr($f,0,-1);
        $rowUP = $standardsTarifRows[0]['row'];

        for ($i = 4; $i<=$this->columnsPattern ; $i++){
            $L = Coordinate::stringFromColumnIndex($i);

            $formula = str_replace("(L)",$L,$f);
            $this->insertValue($rowUP,$i ,$formula);
        }
        $col = 2;
        $this->insertValue($rowUP,$col ,$nameCol_2);

        $col = 3;
        $this->insertValue($rowUP,$col ,$nameCol_3);
    }

    private function totalRow($rowUP,$rowDOWN,$name)
    {

        $col = 3;
        $this->insertValue($rowDOWN+1,$col ,$name);
        $col = 3;
        $this->insertValue($rowUP-1,$col ,$name);

        $col = 4;
        $L = Coordinate::stringFromColumnIndex($col);
        $this->insertValue($rowDOWN+1,$col ,"=sum({$L}$rowUP:{$L}$rowDOWN)");

        $col = 5;
        $L = Coordinate::stringFromColumnIndex($col);
        $RD = $rowDOWN + 1;
        $this->insertValue($rowDOWN+1,$col ,"=F$RD+G$RD+H$RD+I$RD");
        $col = 6;
        $L = Coordinate::stringFromColumnIndex($col);
        $this->insertValue($rowDOWN+1,$col ,"=sum({$L}$rowUP:{$L}$rowDOWN)");
        $col = 7;
        $L = Coordinate::stringFromColumnIndex($col);
        $this->insertValue($rowDOWN+1,$col ,"=sum({$L}$rowUP:{$L}$rowDOWN)");
        $col = 8;
        $L = Coordinate::stringFromColumnIndex($col);
        $this->insertValue($rowDOWN+1,$col ,"=sum({$L}$rowUP:{$L}$rowDOWN)");
        $col = 9;
        $L = Coordinate::stringFromColumnIndex($col);
        $this->insertValue($rowDOWN+1,$col ,"=sum({$L}$rowUP:{$L}$rowDOWN)");
        $col = 12;
        $L = Coordinate::stringFromColumnIndex($col);
        $this->insertValue($rowDOWN+1,$col ,"={$L}$rowDOWN");
        $col = 13;
        $L = Coordinate::stringFromColumnIndex($col);
        $this->insertValue($rowDOWN+1,$col ,"={$L}$rowDOWN");
        $col = 14;
        $L = Coordinate::stringFromColumnIndex($col);
        $this->insertValue($rowDOWN+1,$col ,"={$L}$rowDOWN");
        $col = 15;
        $L = Coordinate::stringFromColumnIndex($col);
        $this->insertValue($rowDOWN+1,$col ,"={$L}$rowDOWN");

        $col = 16;
        $L = Coordinate::stringFromColumnIndex($col);
        $this->insertValue($rowDOWN+1,$col ,"=sum({$L}$rowUP:{$L}$rowDOWN)");
        $col = 17;
        $L = Coordinate::stringFromColumnIndex($col);
        $this->insertValue($rowDOWN+1,$col ,"=sum({$L}$rowUP:{$L}$rowDOWN)");
        $col = 18;
        $L = Coordinate::stringFromColumnIndex($col);
        $this->insertValue($rowDOWN+1,$col ,"=sum({$L}$rowUP:{$L}$rowDOWN)");
        $col = 19;
        $L = Coordinate::stringFromColumnIndex($col);
        $this->insertValue($rowDOWN+1,$col ,"=sum({$L}$rowUP:{$L}$rowDOWN)");
        $col = 20;
        $L = Coordinate::stringFromColumnIndex($col);
        $this->insertValue($rowDOWN+1,$col ,"=sum({$L}$rowUP:{$L}$rowDOWN)");
        $col = 21;
        $L = Coordinate::stringFromColumnIndex($col);
        $this->insertValue($rowDOWN+1,$col ,"=sum({$L}$rowUP:{$L}$rowDOWN)");
        $col = 22;
        $L = Coordinate::stringFromColumnIndex($col);
        $this->insertValue($rowDOWN+1,$col ,"=sum({$L}$rowUP:{$L}$rowDOWN)");
        $col = 23;
        $L = Coordinate::stringFromColumnIndex($col);
        $this->insertValue($rowDOWN+1,$col ,"=sum({$L}$rowUP:{$L}$rowDOWN)");
        $col = 26;
        $L = Coordinate::stringFromColumnIndex($col);
        $this->insertValue($rowDOWN+1,$col ,"=sum({$L}$rowUP:{$L}$rowDOWN)");
        $col = 29;
        $L = Coordinate::stringFromColumnIndex($col);
        $this->insertValue($rowDOWN+1,$col ,"=sum({$L}$rowUP:{$L}$rowDOWN)");
        $col = 29;
        $L = Coordinate::stringFromColumnIndex($col);
        $this->insertValue($rowDOWN+1,$col ,"=sum({$L}$rowUP:{$L}$rowDOWN)");

        $rowDOWN_1= $rowDOWN+1;
        $this->insertValue($rowDOWN+1,30,"=(G$rowDOWN_1+H$rowDOWN_1)*K$rowDOWN_1",false);
        $this->insertValue($rowDOWN+1,31,"=AD$rowDOWN_1-(X$rowDOWN_1+AA$rowDOWN_1)",false);
        $this->insertValue($rowDOWN+1,32,"=AG$rowDOWN_1+AH$rowDOWN_1+AI$rowDOWN_1",false);

        $col = 33;
        $L = Coordinate::stringFromColumnIndex($col);
        $this->insertValue($rowDOWN+1,$col ,"=sum({$L}$rowUP:{$L}$rowDOWN)");
        $col = 34;
        $L = Coordinate::stringFromColumnIndex($col);
        $this->insertValue($rowDOWN+1,$col ,"=sum({$L}$rowUP:{$L}$rowDOWN)");
        $col = 35;
        $L = Coordinate::stringFromColumnIndex($col);
        $this->insertValue($rowDOWN+1,$col ,"=sum({$L}$rowUP:{$L}$rowDOWN)");
        $col = 36;
        $L = Coordinate::stringFromColumnIndex($col);
        $this->insertValue($rowDOWN+1,$col ,"=sum({$L}$rowUP:{$L}$rowDOWN)");
        $col = 37;
        $L = Coordinate::stringFromColumnIndex($col);
        $this->insertValue($rowDOWN+1,$col ,"=sum({$L}$rowUP:{$L}$rowDOWN)");
        $col = 38;
        $L = Coordinate::stringFromColumnIndex($col);
        $this->insertValue($rowDOWN+1,$col ,"=sum({$L}$rowUP:{$L}$rowDOWN)");
        $col = 39;
        $L = Coordinate::stringFromColumnIndex($col);
        $this->insertValue($rowDOWN+1,$col ,"=sum({$L}$rowUP:{$L}$rowDOWN)");
        $col = 40;
        $L = Coordinate::stringFromColumnIndex($col);
        $this->insertValue($rowDOWN+1,$col ,"=sum({$L}$rowUP:{$L}$rowDOWN)");
        $col = 41;
        $L = Coordinate::stringFromColumnIndex($col);
        $this->insertValue($rowDOWN+1,$col ,"=sum({$L}$rowUP:{$L}$rowDOWN)");
        $col = 42;
        $L = Coordinate::stringFromColumnIndex($col);
        $this->insertValue($rowDOWN+1,$col ,"=sum({$L}$rowUP:{$L}$rowDOWN)");
        $col = 43;
        $L = Coordinate::stringFromColumnIndex($col);
        $this->insertValue($rowDOWN+1,$col ,"=sum({$L}$rowUP:{$L}$rowDOWN)");
        $col = 44;
        $L = Coordinate::stringFromColumnIndex($col);
        $this->insertValue($rowDOWN+1,$col ,"=sum({$L}$rowUP:{$L}$rowDOWN)");
        $col = 45;
        $L = Coordinate::stringFromColumnIndex($col);
        $this->insertValue($rowDOWN+1,$col ,"=sum({$L}$rowUP:{$L}$rowDOWN)");
        $col = 46;
        $L = Coordinate::stringFromColumnIndex($col);
        $this->insertValue($rowDOWN+1,$col ,"=sum({$L}$rowUP:{$L}$rowDOWN)");

        $row = $rowUP - 1;
        $this->SheetResult->getStyle("A{$row}:AU{$row}")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('00b0f0');

        $RD = $rowDOWN + 1;
        for ($i = 4; $i<=$this->columnsPattern ; $i++){
            $L = Coordinate::stringFromColumnIndex($i);
            $this->insertValue($rowUP - 1,$i ,"=$L$RD");
        }

        $row = $rowDOWN + 1;
        $this->SheetResult->getStyle("A{$row}:AU{$row}")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('92d050');

    }


    private function insertRow($DA,$dev = 0)
    {

//        $this->insertValue($this->gRow,1 ,$DA['id_tarif']);
        $this->insertValue($this->gRow,3 ,$DA['name_street'].' '.$DA['house']);
        $this->insertValue($this->gRow,4 ,1);
        $this->insertValue($this->gRow,5 ,"=G(0)+H(0)+I(0)",true);
        $this->insertValue($this->gRow,6 ,$DA['area_ur']);
        $this->insertValue($this->gRow,7 ,$DA['value4']);
        $this->insertValue($this->gRow,8 ,$DA['value5']);
        $this->insertValue($this->gRow,9 ,$DA['value6']);
        if ($dev == 0)
            $this->insertValue($this->gRow,11,$DA['standardGkal']);
        else
            $this->insertValue($this->gRow,11,"=T(0)/E(0)",true);

        $this->insertValue($this->gRow,12,$DA['tarif3']);
        $this->insertValue($this->gRow,13,$DA['tarif1']);
        $this->insertValue($this->gRow,14,$DA['tarif2']);
        $this->insertValue($this->gRow,15,"=L(0)",true);

        $this->insertValue($this->gRow,16,$DA['calc_method']);
        $this->insertValue($this->gRow,17,$DA['value_ODPU']);
        $this->insertValue($this->gRow,18,"=Q(0)+P(0)",true);
        $this->insertValue($this->gRow,19,$DA['value_UR_IPU']);
        if ($dev == 0)
            $this->insertValue($this->gRow,20,"=K(0)*E(0)",true);
        else{
            //$this->insertValue($this->gRow,20,"=Q(0)-S(0)",true);
            $this->insertValue($this->gRow,20,"=R(0)-S(0)",true);
        }


        $this->insertValue($this->gRow,21,"=T(0)*L(0)",true);
        $this->insertValue($this->gRow,22,"=E(0)*K(0)",true);
        $this->insertValue($this->gRow,23,"=G(0)*K(0)",true);
        $this->insertValue($this->gRow,24,"=G(0)*K(0)",true);
        $this->insertValue($this->gRow,25,"=W(0)-X(0)",true);
        $this->insertValue($this->gRow,26,"=H(0)*K(0)",true);
        $this->insertValue($this->gRow,27,"=H(0)*K(0)",true);
        $this->insertValue($this->gRow,28,"=Z(0)-AA(0)",true);
        $this->insertValue($this->gRow,29,"=I(0)*K(0)",true);
        $this->insertValue($this->gRow,30,"=(G(0)+H(0))*K(0)",true);
        $this->insertValue($this->gRow,31,"=AD(0)-(X(0)+AA(0))",true);
        $this->insertValue($this->gRow,32,"=AG(0)+AH(0)+AI(0)",true);
        $this->insertValue($this->gRow,33,"=M(0)*W(0)",true);
        $this->insertValue($this->gRow,34,"=N(0)*Z(0)",true);
        $this->insertValue($this->gRow,35,"=O(0)*AC(0)",true);
        $this->insertValue($this->gRow,36,"=U(0)-AF(0)",true);
        //$this->insertValue($this->gRow,37,"",true);
        $this->insertValue($this->gRow,38,"=AJ(0)/1.2",true);
        $this->insertValue($this->gRow,39,"=(AD(0)*L(0)-(W(0)*M(0)+Z(0)*N(0)))/1.2",true);
        $this->insertValue($this->gRow,40,"=AM(0)-AL(0)",true);
        $this->insertValue($this->gRow,41,"=(X(0)*M(0)+AA(0)*N(0))",true);
        $this->insertValue($this->gRow,42,"=(X(0)+AA(0))*O(0)",true);
        $this->insertValue($this->gRow,43,"=(AP(0)-AO(0))/1.2",true);
        $this->insertValue($this->gRow,44,"=AL(0)-AQ(0)",true);


    }

    private function insertValue($row,$col,$value,$f=false)
    {
        if ($f){
            $value = str_replace("(0)",$this->gRow,$value);
        }
        $cells = Coordinate::stringFromColumnIndex($col).(string)($row);
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

    private function footerReport()
    {
        $this->gRow = $this->gRow + 5;

        // подписи для субсидии
        $this->insertValue($this->gRow,1,"Подписи :",false);
        $this->gRow ++;
        $this->insertValue($this->gRow,1,"Производитель :",false);
        $this->gRow ++;
        $this->insertValue($this->gRow,1,'ООО "ЭнергоКомпания"',false);
        $this->insertValue($this->gRow,33,'Служба:',false);
        $this->gRow ++;
        $this->insertValue($this->gRow,33,'Директор МКУ "СЗ ЖКХ"',false);
        $this->insertValue($this->gRow,36,' ______________ Д.А.Соловьев',false);
        $this->gRow ++;
        $this->insertValue($this->gRow,1,"______________________ Д.В.Игошин",false);
        $this->gRow ++;
        $this->gRow ++;
        $this->insertValue($this->gRow,1,"Нач.ПЭО________________ Л.Д.Сафонова",false);
        $this->insertValue($this->gRow,33,'Начальник ПЭО',false);
        $this->insertValue($this->gRow,36,'______________ М.Е.Праздников',false);
        $this->gRow ++;
        $this->gRow ++;
        $this->insertValue($this->gRow,1,"Вед.специалист по ПСХ________________ Ю.Н.Вандакурова",false);
        $this->insertValue($this->gRow,33,'Главный  инженер',false);
        $this->insertValue($this->gRow,36,'______________Н.П.Улаев',false);

        // подписи для субвенции
        $this->gRow = $this->gRow + 5;
        $this->insertValue($this->gRow,1,"Заместитель Главы Беловского",false);
        $this->gRow ++;
        $this->insertValue($this->gRow,1,"городского округа по ЖКХ ",false);
        $this->insertValue($this->gRow,32,"С.В.Смараков ",false);
        $this->gRow ++;
        $this->gRow ++;
        $this->gRow ++;
        $this->gRow ++;
        $this->gRow ++;
        $this->insertValue($this->gRow,1,"Начальник управления Бухгалтерского учета",false);
        $this->gRow ++;
        $this->insertValue($this->gRow,1,"и отчетности Администрации Беловского городского округа ",false);
        $this->insertValue($this->gRow,32,"Н.А.Овчинникова",false);
        $this->gRow ++;
        $this->gRow ++;
        $this->gRow ++;
        $this->gRow ++;
        $this->gRow ++;
        $this->insertValue($this->gRow,1,"Исполнитель М.Е.Праздников",false);
        $this->gRow ++;
        $this->insertValue($this->gRow,1,"8(38452)61515",false);
    }


    public function setDataHead($arrayDataHead)
    {
        $this->DH = $arrayDataHead;
    }


    /**
     * @param mixed $dataArray
     */
    public function setDataArray($dataArray)
    {
        $this->dataArrayST = $dataArray['ST'];
        $this->dataArrayDEV = $dataArray['DEV'];
        $this->dataArrayOTH = $dataArray['OTH'];
        $this->countST = $dataArray['count_ST'];
        $this->countDEV = $dataArray['count_DEV'];
        $this->countOTH = $dataArray['count_OTH'];

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
        $loadFile = "$this->excelPatternPath/$this->excelPatternName";
        $fileName = "$this->resultFilePath/{$this->resultFileName}_tmp.xlsx";
        unlink ($fileName);
        copy($loadFile,$fileName); // копируем файл шаблона с именем результирующешо. и открываем его для модификации

        $loadFile = $fileName;

        $this->spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $this->spreadsheet = $this->spreadsheet->load($loadFile);

        $styleArray = array(
            'font'  => array(
                'size'  => 12,
                'name'  => 'Times New Roman'
            ));
        $this->spreadsheet->getDefaultStyle()
            ->applyFromArray($styleArray);
        $this->SheetResult = $this->spreadsheet->getActiveSheet();
        $this->SheetResult->getDefaultRowDimension()->setRowHeight(12);
    }



    public function saveFile()
    {
        $fileName = "$this->resultFilePath/$this->resultFileName.xlsx";
        unlink ($fileName);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->spreadsheet, "Xlsx");
        $writer->save($fileName);
        $fileName = "$this->resultFilePath/{$this->resultFileName}_tmp.xlsx";
        unlink ($fileName);
    }





}