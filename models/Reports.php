<?php

namespace models;


class Reports
{
    private $serverName;
    private $wait;
    private $conn;
    private $GUID_Report;

    function __construct()
    {
        $this->conn = new \backend\Connection(true);
        $this->serverName = "http://13.14.0.190/index.php";

        $this->wait = 0;
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
            'host'          => $security->getThisFtpServerHost(),
            'login'         => $security->getThisFtpServerLogin(),
            'pass'          => $security->getThisFtpServerPassword(),

            "DB_serverName" => $security->getServerName_DB(),
            "DB_dataBase"   => $security->getDataBase_DB(),
            "DB_userName"   => $security->getUserName_DB(),
            "DB_password"   => $security->getPassword_DB(),

            'dirDestination'=> $security->getDownloadDir()

        );

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