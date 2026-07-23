<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\LS\HOT\ResultXlsODPU;


use DB\Connection;
use DB\Table\device_HOT_xls;
use DB\Table\device_HOT_xls_byRow;
use properties\security;

class VIEW extends \Reports\reportView
{

    private $book;

    /**
     * @var \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     */
    private $Sheet;

    private $row;

    private $fileName;

    public function openFile()
    {

        $path = security::DIR;
        $book = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xls");
        $book->setReadDataOnly(false);
        $this->fileName = "$path/ImpExp/".$this->id_report;
        $this->book = $book->load($this->fileName);
        $this->book->setActiveSheetIndex(0);
        $this->Sheet = $this->book->getActiveSheet();
    }

    public function addData()
    {
        $this->row = 70;
        $this->addListExclude();
        $this->addListFix();
        $this->addTotal();
    }

    public function save()
    {
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->book, 'Xlsx');
        $writer->save($this->fileName);
    }


    public function addListExclude()
    {
        $list = $this->MODEL->getListExclude();
        if (count($list)<1)
            return;
        $d = new device_HOT_xls_byRow();
        $this->setCellValue(4,$this->row, "Даты и объемы исключенные из расчета");
        $this->row ++;
        foreach ($list as $key => $item){
            $this->setCellValue(4,$this->row, date('d.m.Y',strtotime($item[$d::d])));
            $this->setCellValue(10,$this->row, " ".$item[$d::Q_pr]);
            $this->row ++;

        }
        $this->row ++;
    }

    public function addListFix()
    {
        $list = $this->MODEL->getListFix();
        if (count($list)<1)
            return;
        $d = new device_HOT_xls_byRow();
        $this->setCellValue(4,$this->row, "Даты по которым произведено усредение");
        $this->row ++;
        foreach ($list as $key => $item){
            $this->setCellValue(4,$this->row, date('d.m.Y',strtotime($item[$d::d])));
            $this->setCellValue(10,$this->row, " ".$item[$d::Q_pr_new]);
            $this->row ++;

        }
        $this->row ++;
    }

    public function addTotal()
    {
        $total = $this->MODEL->getTotal();
        $this->setCellValue(4,$this->row, "Итого ГКал полсе расчетов $total");
    }

    private function setCellValue($cellOrCol, $row = null,$value = '')
    {
        //column set by index
        if(is_numeric($cellOrCol)) {
            $cell = $this->Sheet->getCellByColumnAndRow($cellOrCol, $row);
        } else {
            $lastChar = substr($cellOrCol, -1, 1);
            if(!is_numeric($lastChar)) { //column contains only letter, e.g. "A"
                $cellOrCol .= $row;
            }

            $cell = $this->Sheet->getCell($cellOrCol);
        }
        $cell->setValue($value);
    }
}