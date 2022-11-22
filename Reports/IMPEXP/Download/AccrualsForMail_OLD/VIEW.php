<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */


namespace Reports\IMPEXP\Download\AccrualsForMail_OLD;



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
        copy ( __DIR__.'\\post1.dbf', $this->resultFilePath.'/'.$this->nameReport.$this->extensionName );

        $db = dbase_open($this->resultFilePath.'/'.$this->nameReport.$this->extensionName, 2);

        while ($res = $this->data->fetch()){
            foreach ($res as $key => $value){
                $res[$key] = mb_convert_encoding($value,'cp866', 'utf-8');
            }
            $row = array_values($res);
            dbase_add_record ($db,$row);
        }
        dbase_close($db);
    }




}