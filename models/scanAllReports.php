<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 16.04.2020
 * Time: 14:19
 */

namespace models;


use Views\mPrint;

class scanAllReports
{

    private $conn;
    private $files;
    function __construct()
    {
        $this->conn = new \DB\Connect(\properties\security::SECURITY);

    }


    public function registerAllElements()
    {
        \Views\mPrint::R(__METHOD__);
        $this->files = array();
        $this->scanDirs(\properties\security::DOCUMENT_ROOT_PATH."/Reports","/Reports","Reports");

    }


    private function scanDirs($start,$path,$dir)
    {
        $handle = opendir($start);
        while (false !== ($file = readdir($handle)))
        {
            if ($file != '.' && $file != '..')
            {
                if (is_dir($start.'/'.$file))
                {
                    $this->scanDirs($start.'/'.$file,$path."/".$file,$file);

                }
                else
                {
                    if ($file=="Control.php"){
                        array_push($this->files , $path."/".$file);
                        mPrint::R("methods $path");


                        $class = $path."/Control";
                        $class = str_replace("/","\\",$class);

                      //  $class="\\Reports$this->parent\\$this->route\\Control";
                        $Object= new $class('');
                        $path1 = str_replace("/","\\",$path);

                        try {
                            $this->conn->table("reports")
                                ->set("report",$path1)
                                ->insert();
                        }catch (\PDOException $e){
                            mPrint::R("Ключь уже есть");
                        }

                        $this->conn->table("reports")
                            ->where ("report",$path1)
                            ->set("name",$Object->getDescriptionReport())
                            ->set("description",$Object->getDescriptionReport())
                            ->set("manageTable",$Object->getManageTable())
                            ->update();
                        unset($Object);
                    }
                }
            }
        }
        closedir($handle);
    }
}