<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 05.06.2020
 * Time: 10:04
 */

namespace Reports;


use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

abstract class reportView
{
    public $id_report;

    public $nameReport;
    public $extensionName;

    public $PatternName;
    public $resultFilePath;
    public $resultFileName;
    public $section;
    public $H;
    public $data;

    public $objectDoc;
    /**
     * @var \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     */
    public $SheetResult;

    public $spreadsheet;
    
    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @param mixed $dataHead
     */
    public function setDataHead($dataHead)
    {
        $this->H = $dataHead;
    }


    /**
     * @param mixed $PatternName
     */
    public function setPatternName($PatternName)
    {
        $this->PatternName = $PatternName;
    }

    /**
     * @param mixed $resultFilePath
     */
    public function setResultFilePath($resultFilePath)
    {
        $this->resultFilePath = $resultFilePath;
    }

    /**
     * @param mixed $resultFileName
     */
    public function setResultFileName($resultFileName)
    {
        $this->resultFileName = $resultFileName;
    }


}