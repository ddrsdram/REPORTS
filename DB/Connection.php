<?php

namespace DB;

/**
 * Class Connection
 * @package backend
 *
 * отвеччает за соединение с БД и реализовывает
 * inset, update, delete, exec
 */
abstract class Connection
{
    const SECURITY = 'SECURITY';
    const GD = 'GLOBAL';
    const SITE = 'SITE';

    private $MSSQL;
    private $typeFetch = \PDO::FETCH_ASSOC;

    private $dbh;

    private $stmt;

    private $table;
    private $orderBy;
    private $groupBy;
    public $lastInsertID;

    public $idSet;
    private $set;

    private $idWhere;
    private $where=array();

    private $arrayConnectionSettings;

     function __construct($arrayConnectionSettings = false,$MSSQL = true)
    {

        $this->MSSQL = $MSSQL;

        $security = new \properties\security();
        $this->arrayConnectionSettings = $security->getRight($arrayConnectionSettings);

        $t_arr = explode ('\\',static::class);
        $this->table(array_pop ($t_arr));

    }

    private function Conn()
    {
        //error_reporting(0);

        if ($this->MSSQL)
            $this->connPDO();
        else
            $this->connPDO_MySQL();

        //error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
    }

    private function connPDO_MySQL(){
        try{

            $this->dbh = new \PDO("mysql:host=" . $this->arrayConnectionSettings["serverName"] . ";dbname=" . $this->arrayConnectionSettings["dataBase"].";charset=utf8",
                $this->arrayConnectionSettings["userName"], $this->arrayConnectionSettings["password"],
                array("charset" => "UTF-8"));
        }catch (\PDOException $e){
            print "===================================error==========================================";
//    $e->getMessage();
            exit;
        }
    }

    private function connPDO()
    {

        try{
            $this->dbh = new \PDO("sqlsrv:Server=" . $this->arrayConnectionSettings["serverName"] . ";Database=" . $this->arrayConnectionSettings["dataBase"].";Encrypt=0;TrustServerCertificate=1"
                , $this->arrayConnectionSettings["userName"], $this->arrayConnectionSettings["password"]);
        }catch (\PDOException $e){
            print "===================================error==========================================";
            // $e->getMessage();
            exit;
        }
    }

    /*
    *******************************************
    *******************************************
    *******************************************
    */
    public function table($nameTable)
    {
        $this->table=$nameTable;
        $this->init();
        return $this;
    }

    private function init()
    {
        $this->idSet=-1;
        $this->set=array();
        $this->idWhere=-1;
        $this->where=array();
        $this->orderBy = false;
        $this->groupBy = false;
    }

    public function where($var,$data,$znak="=",$logical='and')
    {
        $this->idWhere++;
        $this->where[$this->idWhere]['variable']=$var;
        $this->where[$this->idWhere]['data']=$data;
        $this->where[$this->idWhere]['znak']=$znak;
        $this->where[$this->idWhere]['logical']=$logical;
        return $this;
    }

    public function set($var,$data,$binary = false)
    {
        $this->idSet++;
        $this->set[$this->idSet]['variable']=$var;
        $this->set[$this->idSet]['data']=$data;
        $this->set[$this->idSet]['binary']=$binary;

        return $this;
    }

    /**
     * @param $orderBy string поля которые будут участвовать вв сортировке
     * @return $this
     */
    public function orderBy($orderBy)
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    /**
     * @param int $typeFetch
     */
    public function setTypeFetch(int $typeFetch)
    {
        $this->typeFetch = $typeFetch;
    }

    public function fetch($goTop=1)
    {
        if ($goTop==0){
            $ret=$this->stmt->fetch($this->typeFetch, \PDO::FETCH_ORI_FIRST);
        }else {
            $ret=$this->stmt->fetch($this->typeFetch);
        }
        if ($ret){
            return $ret;
            //return $this->IconvArray($ret);
        } else return false;

    }

    public function fetchField($filed)
    {
        if (isset($filed)){
            if ($res=$this->fetch()){
                return $res[$filed];
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function fetchAll()
    {
        return $this->stmt->fetchAll($this->typeFetch);
    }

    public function groupBy($groupBy)
    {
        $this->groupBy = $groupBy;
        return $this;
    }

    /*
     * ***********************************************************************************
     * ***********************************************************************************
     * ***********************************************************************************
     */
    public function complexSelect()
    {
        $this->Conn();

        $query= $this->table;
        $this->stmt = $this->dbh->prepare($query);

        if ($this->idWhere>=0) {
            for ($i = 0; $i <= $this->idWhere; $i++) {
                $param = ':'.$this->where[$i]['variable'];
                $data=$this->where[$i]['data'];
                $this->stmt->bindValue($param , $data);

            }
        }

        $this->stmt->execute();
        return $this;
    }




    public function select($fileds=" * ")
    {
        $this->Conn();

        $textWhere="";
        if ($this->idWhere>=0){
            $textWhere=" where  ";
            for ($i=0;$i<=$this->idWhere;$i++){
                $_if = $this->where[$i]['data']!=Null ? " :var".$i : 'Null';
                $textWhere =$textWhere. "(" . $this->where[$i]['variable']. " ".$this->where[$i]['znak'].$_if.")".($i+1>$this->idWhere?"":" {$this->where[$i]['logical']} ");
            }

        }
        $orderBy = $this->orderBy ? " ORDER BY ".$this->orderBy : "";
        $groupBy = $this->groupBy ? " GROUP BY ".$this->groupBy : "";

        $query= "select  ".$fileds." from " . $this->table . " ".$textWhere." ".$groupBy." ".$orderBy ;

        $this->stmt = $this->dbh->prepare($query);

        if ($this->idWhere>=0) {
            for ($i = 0; $i <= $this->idWhere; $i++) {
                $param = ':var' .$i;
                $data=$this->where[$i]['data'];
                if ($data!=Null){
                    $this->stmt->bindValue($param , $data);
                }

            }
        }

        $this->init();
        $this->stmt->execute();

        return $this;
    }

    public function delete ()
    {
        $this->Conn();

        $textWhere="";
        if ($this->idWhere>=0){
            $textWhere=" where  ";
            for ($i=0;$i<=$this->idWhere;$i++){
                $textWhere =$textWhere. "(" . $this->where[$i]['variable']. " ".$this->where[$i]['znak']." :var".$i.")".($i+1>$this->idWhere?"":" and ");
            }

        }
        if ($this->MSSQL)
            $commandDelete="delete ";
        else
            $commandDelete="delete from ";

        $query= $commandDelete . $this->table . " ".$textWhere;
        $this->stmt = $this->dbh->prepare($query);

        if ($this->idWhere>=0) {
            for ($i = 0; $i <= $this->idWhere; $i++) {
                $param = ':var' .$i;
                $data=$this->where[$i]['data'];
                $this->stmt->bindValue($param , $data);
            }
        }
        $this->init();
        $this->stmt->execute();

    }

    public function complexQuery($query)
    {
        $this->Conn();

        //$query=iconv('UTF-8','windows-1251', $query);
        $this->stmt = $this->dbh->prepare("$query"); //
        $this->init();
        $this->stmt->execute();
        return $this;
    }

    public function update()
    {
        $this->Conn();

        $textWhere="";
        if ($this->idWhere>=0){
            $textWhere=" where  ";
            for ($i=0;$i<=$this->idWhere;$i++){
                $textWhere =$textWhere. "(" . $this->where[$i]['variable']. " ".$this->where[$i]['znak']." :".$this->where[$i]['variable']."_w )".($i+1>$this->idWhere?"":" and ");
            }

        }
        $textSET="";
        if ($this->idSet>=0){
            $textSET=" set  ";
            for ($i=0;$i<=$this->idSet;$i++){
                $textSET =$textSET. $this->set[$i]['variable']. " =   :".$this->set[$i]['variable']." ".($i+1>$this->idSet?"":" , ");
            }

        }


        $query= "update " . $this->table." ".$textSET." ".$textWhere;
        $this->stmt = $this->dbh->prepare("$query");
        for ($i=0;$i<=$this->idSet;$i++){
            $this->stmt->bindParam(':'.$this->set[$i]['variable'], $this->set[$i]['data']);
        }
        for ($i=0;$i<=$this->idWhere;$i++){
            $this->stmt->bindParam(':'.$this->where[$i]['variable']."_w", $this->where[$i]['data']);
        }

        $this->init();
        $res=$this->stmt->execute();
        unset($stmt);
        return $res;

    }


    public function insert()
    {
        $this->Conn();

        $textVar="";
        $textData="";
        if ($this->idSet>=0){
            for ($i=0;$i<=$this->idSet;$i++){
                $textVar = $textVar. $this->set[$i]['variable']." ".($i+1>$this->idSet?"":" , ");
                $textData = $textData.":".$this->set[$i]['variable']." ".($i+1>$this->idSet?"":" , ");
            }

        }
        $query= "INSERT INTO " . $this->table . " (".$textVar. ") values(".$textData.");";


        $stmt = $this->dbh->prepare("$query");
        for ($i=0;$i<=$this->idSet;$i++){
            if ($this->set[$i]['binary'])
                $stmt->bindParam(':'.$this->set[$i]['variable'], $this->set[$i]['data'], \PDO::PARAM_LOB, 0, \PDO::SQLSRV_ENCODING_BINARY);
            else
                $stmt->bindParam(':'.$this->set[$i]['variable'], $this->set[$i]['data']);
        }

        $this->init();
        $stmt->execute();


        unset($stmt);

        $ret = $this->dbh->lastInsertId();
        return $ret;
    }


    public function SQLExec()
    {
        $this->Conn();

        $query= 'exec '. $this->table.' ';

        for ($i=0;$i<=$this->idSet;$i++){
            $query =$query. " :".$this->set[$i]['variable']." ".($i+1>$this->idSet?"":" , ");
        }

        $this->stmt = $this->dbh->prepare($query);

        for ($i=0;$i<=$this->idSet;$i++){
            $this->stmt->bindParam(':'.$this->set[$i]['variable'], $this->set[$i]['data']);

        }
        $this->lastInsertID = $this->dbh->lastInsertId();
        $this->init();
        $this->stmt->execute();

        return $this;
    }
}