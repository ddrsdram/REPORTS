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
        $range = 'D65:AY81';

// Если были старые объединения - убрать их
        foreach ($this->Sheet->getMergeCells() as $mergedRange) {
            if ($mergedRange == $range) {
                $this->Sheet->unmergeCells($mergedRange);
            }
        }

        $this->Sheet->mergeCells($range);

        $this->Sheet->getStyle($range)->applyFromArray([
            'font' => [
                'color' => [
                    'rgb' => 'FF0000' // красный цвет
                ],
                'size' => 14,
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                'wrapText' => true
            ]
        ]);
        $formula = $this->MODEL->getFormula();
        $formula = str_replace('#',chr(13).'Минус ГВС'.chr(13),$formula);
        $total = $this->MODEL->getTotal();
        $this->Sheet->getStyle($range)
            ->getAlignment()
            ->setWrapText(true);
        $this->setCellValue('D65',value: "Qотоп = $formula ".chr(13)."= $total ГКал");

    }

    public function save()
    {
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->book, 'Xlsx');
        $writer->save($this->fileName);
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