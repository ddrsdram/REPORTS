<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */


namespace Reports\FIO\RegisterOfRegistered;



class VIEW extends \Reports\reportView
{

    function init()
    {
        $path = $_SERVER['DOCUMENT_ROOT'];
        $this->resultFilePath = $path."/ImpExp";
        $this->objectDoc = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $this->SheetResult = $this->objectDoc->getActiveSheet();

    }


    public function saveFile()
    {

        $fileName = "$this->resultFilePath/$this->resultFileName$this->extensionName";
        unlink($fileName);
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->objectDoc, "Xlsx");
        $objWriter->save($fileName);

    }

    public function FillingInData()
    {
        $this->FillingInHead();

    }

    public function FillingInHead()
    {
        $this->SheetResult->getColumnDimensionByColumn(1)->setWidth(3);
        $this->SheetResult->getColumnDimensionByColumn(2)->setWidth(21);
        $this->SheetResult->getColumnDimensionByColumn(3)->setWidth(7);
        $this->SheetResult->getColumnDimensionByColumn(4)->setWidth(3);
        $this->SheetResult->getColumnDimensionByColumn(5)->setWidth(30);
        $this->SheetResult->getColumnDimensionByColumn(6)->setWidth(9);
        $this->SheetResult->getColumnDimensionByColumn(7)->setWidth(12);
        $this->SheetResult->getColumnDimensionByColumn(8)->setWidth(10);
    }

}