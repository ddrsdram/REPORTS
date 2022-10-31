<?php
namespace DB\Construct;

use \DB\Connection;


class DataBase_to_PHP
{
    private $objectDB = null;

    private $typeObject = null;

    private $fileHandle = null;

    private $pathObject = null;
    const Table = "Table";
    const View = "View";

    public function __construct()
    {
        $this->pathObject = $_SERVER['DOCUMENT_ROOT']."\\DB\\";
    }

    /**
     * @param mixed $objectDB
     */
    public function setObjectDB($objectDB): void
    {
        $this->objectDB = $objectDB;
    }

    /**
     * @param null $typeObject
     */
    public function setTypeObject($typeObject = self::Table): void
    {
        $this->typeObject = $typeObject;
    }

    public function constructObject()
    {

        if ($this->typeObject == null)
            $this->typeObject = self::Table;


        if ($this->objectDB == null){
            $ObjectsDB_array = $this->getAllObjectsFromDB();
        }else
            $ObjectsDB_array[] = $this->objectDB;

        $this->pathObject = $this->pathObject .  $this->typeObject."\\";

        foreach ($ObjectsDB_array as $item){
            $this->runProcess($item);
        }

    }

    private function getAllObjectsFromDB()
    {
        $conn = new Conn();
        $typeObject = "";
        switch ($this->typeObject) {
            case self::Table:
                $typeObject = "U";
                break;
            case self::View:
                $typeObject = "V";
                break;

        }

        $query = "SELECT name FROM sys.objects
                    WHERE type in (N'$typeObject') 
                    and name not like '%!%'
                    and name not like '%$%'
                    order by name
                ";
        $data  = $conn->complexQuery($query);
        $ret = Array();
        while ($res = $data->fetch()){
            $ret[] = $res['name'];
        }
        return $ret;
    }

    private function runProcess($objectDB)
    {
        print "create $objectDB \r\n";
        $this->objectDB = $objectDB;
        $this->openFile();
        $this->createHandleFile();
        $this->addFields();
        $this->createEndFile();

    }

    private function openFile()
    {
        $this->fileHandle = fopen($this->pathObject.$this->objectDB.".php", 'wb+');
    }

    private function  createHandleFile()
    {
        fwrite($this->fileHandle,"<?php\r\n\r\n");
        fwrite($this->fileHandle,"namespace DB\\{$this->typeObject};\r\n\r\n\r\n");
        fwrite($this->fileHandle,"use \DB\Connection;\r\n\r\n");
        fwrite($this->fileHandle,"class {$this->objectDB} extends Connection\r\n");
        fwrite($this->fileHandle,"{\r\n");

    }


    private function addFields()
    {
        $conn1 = new Conn();
        $data1 = $conn1->table("INFORMATION_SCHEMA.COLUMNS")
            ->where("TABLE_NAME",$this->objectDB)
            ->orderBy("ORDINAL_POSITION")
            ->select();

        $data1 = $data1->fetchAll();
        foreach ($data1 as $key => $res){
            $this->addFieldsInToObject($res);
        }
    }



    private function addFieldsInToObject($res)
    {
        $COLUMN_NAME = $res['COLUMN_NAME'];
        fwrite($this->fileHandle,chr(9)."const {$COLUMN_NAME} =  '$COLUMN_NAME';\r\n\r\n");
    }

    private function  createEndFile()
    {
        fwrite($this->fileHandle,"}\r\n");
        fclose($this->fileHandle);
    }
}
