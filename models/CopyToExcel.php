<?php
namespace models;
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 16.02.2021
 * Time: 8:04
 */
class CopyToExcel
{
    /**
     * @var \backend\Connection
     */
    private $data_class;
    private $data_array;
    private $path,$fileName;
    private $spreadsheet,$SheetResult;

    /**
     * @param mixed $data_array
     * @return $this
     */
    public function setDataArray($data_array)
    {
        $this->data_array = $data_array;
        return $this;
    }

    /**
     * @param $data_class
     * @return $this
     */
    public function setDataClass($data_class)
    {
        $this->data_class = $data_class;
        $this->data_array = $this->data_class->fetchAll();
        return $this;
    }

    /**
     * @param $fileName
     * @return $this
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * @param $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    public function save()
    {
        $this->spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $this->SheetResult = $this->spreadsheet->getActiveSheet();

        $row = 1;
        foreach($this->data_array as $r => $res){
            $col = 1;
            $row = $row + 1;
            foreach ($res as $key => $value){
                if ($row - 1  == 1){
                    $this->setCellValue($col,$row-1,$key);
                }
                $this->setCellValue($col,$row,$value);
                $col = $col + 1;
            }
        }

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->spreadsheet, "Xlsx");
//        $writer->setPreCalculateFormulas(false);
        $savePath = $this->path.$this->fileName;

        $writer->save($savePath);
    }


    private function setCellValue($cellOrCol, $row = null, $value = '')
    {
        //column set by index
        if (is_numeric($cellOrCol)) {
            $cell = $this->SheetResult->getCellByColumnAndRow($cellOrCol, $row);
        } else {
            $lastChar = substr($cellOrCol, -1, 1);
            if (!is_numeric($lastChar)) { //column contains only letter, e.g. "A"
                $cellOrCol .= $row;
            }

            $cell = $this->SheetResult->getCell($cellOrCol);
        }
        $cell->setValue($value);
    }
}