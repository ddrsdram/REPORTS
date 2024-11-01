<?php

namespace models;


class Reports
{
    private $serverName;
    private $wait;
    private $conn;
    private $GUID_Report;

    private $connection_array = false;



    function __construct()
    {
        $this->conn = new \DB\Connect(\properties\security::GD);
        $security = new \properties\security();
        $this->serverName = $security->getPrintServer();

        $this->wait = 0;
    }


    /**
     * @param array $connection_array
     */
    public function setConnectionArray(array $connection_array)
    {
        $this->connection_array = $connection_array;
    }

    /**
     * @param mixed $wait
     */
    public function setWait($wait)
    {
        $this->wait = $wait;
    }


    /**
     * @return mixed
     */
    public function getGUIDReport()
    {
        return $this->GUID_Report;
    }

    public function prepareReport($_report)
    {
        $res = $this->conn->table("proc_createReport")
            ->set("id_user", $_SESSION['id_user'])
            ->set("ORG",$_SESSION['ORG'])
            ->set("report", $_report)
            ->set("wait", $this->wait)
            ->SQLExec()->fetch();
        $this->GUID_Report = $res['id'];
    }

    public function headReport($arrayHead)
    {
        $JESON_arrayHead = json_encode($arrayHead);
        $this->conn->table("reports_register")
            ->set("headArray", $JESON_arrayHead)
            ->where('id',$this->GUID_Report)
            ->update();
    }

    public function setQueryForBody($query)
    {
        $this->conn->table("reports_register")
            ->set("query", $query)
            ->where('id',$this->GUID_Report)
            ->update();
    }

    public function runCreateReport()
    {
        $this->curl_request_async($this->GUID_Report);
    }

    public function curl_request_async($id_reports_register)
    {
        $security = new \properties\security();
        //$security = new \properties\security(true);
        $array = array(
            'id'            => $id_reports_register,
            'port'          => $security->getThisFtpServerPort(),
            'host'          => $security->getThisFtpServerHost(),
            'login'         => $security->getThisFtpServerLogin(),
            'pass'          => $security->getThisFtpServerPassword(),

            "DB_serverName" => $security->getGD_ServerName(),
            "DB_dataBase"   => $security->getGD_DataBase(),
            "DB_userName"   => $security->getGD_UserName(),
            "DB_password"   => $security->getGD_Password(),

            'dirDestination'=> $security->getDownloadDir()

        );

        // Если указаны другие параметры соединения
        if ($this->connection_array !== false){
            $array['DB_serverName']     = $this->connection_array['serverName'];
            $array['DB_dataBase']       = $this->connection_array['dataBase'];
            $array['DB_userName']       = $this->connection_array['userName'];
            $array['DB_password']       = $this->connection_array['password'];
        }

        $ch = curl_init($this->serverName);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $array);

// Или предать массив строкой:
// curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($array, '', '&'));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);

        $html = curl_exec($ch);
        curl_close($ch);;
        return $html;
    }

    public function getInfo($id_report)
    {
        return $this->conn->table("reports_register")
            ->where("id", $id_report)
            ->select()
            ->fetch();
    }


}