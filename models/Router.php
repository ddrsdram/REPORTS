<?php

namespace models;

class Router
{
    private static $_object;
    private $conn;

    private $id_Reports_register;
    private $argument_count;
    private $argument_array;

    private $wait; // = 1 - запускаем немедленно; = 0 - запускаем в фоновом режиме
    private $report;
    private $pathToClass;
    private $dirSource;
    function __construct()
    {
        $this->argument_array = 0;
        $this->argument_array = array();

        set_time_limit(350);
        $this->dirSource = $_SERVER['DOCUMENT_ROOT'].'ImpExp/';

        $this->wait = -1;

        $this->id_Reports_register = 0;
        if (array_key_exists("DB_serverName",  $_REQUEST)){
            $_SESSION["serverName"] =   array_key_exists("DB_serverName",  $_REQUEST)   ?     $_REQUEST["DB_serverName"]  : "127.0.0.1";
            $_SESSION["dataBase"] =     array_key_exists("DB_dataBase",    $_REQUEST)   ?     $_REQUEST["DB_dataBase"]    : "KVPLPHPDEV";
            $_SESSION["userName"] =     array_key_exists("DB_userName",    $_REQUEST)   ?     $_REQUEST["DB_userName"]    : "php1";
            $_SESSION["password"] =     array_key_exists("DB_password",    $_REQUEST)   ?     $_REQUEST["DB_password"]    : "!23QweAsd";
            $arrayConnectionSettings = Array();
            $arrayConnectionSettings["serverName"] =  $_SESSION["serverName"] ;
            $arrayConnectionSettings["dataBase"] =    $_SESSION["dataBase"];
            $arrayConnectionSettings["userName"] =    $_SESSION["userName"];
            $arrayConnectionSettings["password"] =    $_SESSION["password"];
        }else{
            $arrayConnectionSettings = true;
        }

        $this->conn = new \backend\Connection();

        if ($_SERVER['REQUEST_METHOD']=="GET"){
            $this->id_Reports_register = empty($_GET['id'])?0:$_GET['id'];
        }

        if ($_SERVER['REQUEST_METHOD']=="POST"){
            $this->id_Reports_register = empty($_POST['id'])?0:$_POST['id'];
        }


    }


    public function AppRun()
    {
        $this->detectProperties();
        if ($this->getWait()){
            $this->runNow();
        }else{
            $this->runLater();
        }
    }

    private function runNow(){
        $class = $this->report."/Control";
        $class = str_replace("/","\\",$class);
        $Object= new $class($this->id_Reports_register);
        //$Object->setIdReport();
        $Object->run();

        $this->setEndTimeForReport(); // пометим во сколько закончилось формирование отчета

        $ftp = new \models\ftp();

        if (array_key_exists("host",            $_REQUEST)) $ftp->setHost(              $_REQUEST['host']);
        if (array_key_exists("port",            $_REQUEST)) $ftp->setPort(              $_REQUEST['port']);
        if (array_key_exists("login",           $_REQUEST)) $ftp->setLogin(             $_REQUEST['login']);
        if (array_key_exists("pass",            $_REQUEST)) $ftp->setPass(              $_REQUEST['pass']);
        if (array_key_exists("dirDestination",  $_REQUEST)) $ftp->setDirDestination(    $_REQUEST['dirDestination']);
        //dirDestination
        $fileName = $Object->getFileNameReport();
        $ftp ->connection()
            ->setFileSource($fileName)
            ->setFileDestination($this->id_Reports_register)
            ->copy();
        unlink($this->dirSource.$fileName);
    }

    private function runLater(){
        pclose(popen("start /B php C:\OSPanel\domains\Reports\index.php $this->id_Reports_register ", "r"));

        /*
        if (substr(php_uname(), 0, 7) == "Windows"){
            pclose(popen("start /B ". $cmd, "r"));
        }
        else {
            exec($cmd . " > /dev/null &");
        }
    */

    }

    private function getWait()
    {
        if ($this->argument_count > 1) { // если было запущено в параленльном потоке то однозначно непосредственно ожидаем
            $this->wait = 1;
        }

        if($this->wait == 1) {
            return true;
        }else{
            return false;
        }

    }

    private function detectProperties()
    {
        $res = $this->conn->table("View_reports_register")
            ->where("id", $this->id_Reports_register)
            ->select()->fetch();
        $this->wait = $res['wait'];
        $this->report = $res['report'];
    }

    private function setEndTimeForReport()
    {
        $query = "update reports_register set endDate = getdate() where id = '{$this->id_Reports_register}'";
        $res = $this->conn->complexQuery($query);
    }

    /**
     * @param mixed $argument_array
     */
    public function setArgumentArray($argument_array)
    {
        $this->argument_array = $argument_array;
        if ($this->argument_count > 1){
            $this->id_Reports_register = $this->argument_array[1];
        }
    }

    /**
     * @param mixed $argument_count
     */
    public function setArgumentCount($argument_count)
    {
        $this->argument_count = $argument_count;
    }

    public static function get()
    {
        if (!isset(self::$_object)) {
            self::$_object = new self;
        }
        return self::$_object;
    }


}



