<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */


namespace Reports\backUpForORG;



class VIEW extends \Reports\reportView
{

    function __construct()
    {
        $path = $_SERVER['DOCUMENT_ROOT'];
        $this->resultFilePath = $path."/ImpExp";
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    public function fillInFile()
    {
        $db = fopen($this->resultFilePath.'/'.$this->id_report, 'w+');
        while ($res = $this->data->fetch()){
            $str = "";
            foreach ($res as $key => $A){
                $str .= $A."|";
            }
            fwrite ( $db, $str.chr(13).chr(10) );
        }
        fclose($db);
    }




}