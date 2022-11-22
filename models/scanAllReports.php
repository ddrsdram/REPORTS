<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 16.04.2020
 * Time: 14:19
 */

namespace models;


class scanAllReports
{

    private $conn;
    private $files;
    function __construct()
    {
        $this->conn = new \DB\Connect(\DB\Connect::SECURITY);

    }


    public function registerAllElements()
    {
        print __METHOD__;
        print "</br>";
        $this->files = array();
        $this->scanDirs($_SERVER['DOCUMENT_ROOT']."/Reports","/Reports","Reports");

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
                        print "methods $path</br>";

                        $class = $path."/Control";
                        $class = str_replace("/","\\",$class);

                      //  $class="\\Reports$this->parent\\$this->route\\Control";
                        $Object= new $class('');
                        $path1 = str_replace("/","\\",$path);

                        print "</br>";
                        try {
                            $this->conn->table("reports")
                                ->set("report",$path1)
                                ->insert();
                        }catch (\PDOException $e){

                            print "Ключь уже есть</br>";
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