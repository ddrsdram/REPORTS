<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */


namespace Reports\IMPEXP\Download\AccrualsForUralRing;



class VIEW extends \Reports\reportView
{
    private $resultFilePath ;
    /**
     * @var \backend\Connection
     */
    private $data;

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
        $arr = Array();;
        $summa = 0;
        while ($res = $this->data->fetch()){
            $res['SALDON'] = $res['SALDON'] *1;
            $arr[] = $res;

            $summa += $res['SALDON'];
        }

        $db = fopen($this->resultFilePath.'/'.$this->nameReport.$this->extensionName, 'w+');
        fwrite ( $db, "#FileSum $summa".chr(13).chr(10) );
        fwrite ( $db, "#Type 7".chr(13).chr(10) );
        fwrite ( $db, "#Service 1828".chr(13).chr(10) );
        foreach ($arr as $key => $A){
            fwrite ( $db, "{$A['FIO']};{$A['name']},{$A['UL']},{$A['DOM']},{$A['KV']};{$A['LCHET']};{$A['SALDON']};;;;".chr(13).chr(10) );
        }


        fclose($db);
    }




}