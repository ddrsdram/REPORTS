<?php

/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 13.09.2019
 * Time: 16:54
 */
namespace models;

class Modem2
{
    private $flagOpenPort;
    private $port;
    private $objComport;

    function __construct()
    {
        $this->flagOpenPort=false;
        $this->port=false;
        $this->objComport = new \COM("AxSerial.ComPort");
    }

    function __destruct()
    {
        // TODO: Implement __destruct() method.
        $this->objComport->Close();
        print "</br>Close PORT</br>";
    }

    /**
     * @param $port
     * @return $this
     */
    public function setPort($portNumber)
    {
        $this->port = $portNumber;
        return $this;
    }

    /**
     * @return bool
     */
    public function connect()
    {
        $this->flagOpenPort=$this->portOpen();
        $this->objComport->sleep(1000);
        return $this->flagOpenPort;
    }

    public function ser_setDTR($trueOrFalse)
    {
        ser_setDTR($trueOrFalse);
        sleep(1);
    }
    /**
     * @param $textData
     * @return bool
     */
    public function writeData($textData)
    {
        $res=$this->objComport->WriteString($textData."\r");
//        sleep(5);
        return $res;
    }

    /**
     * @return mixed
     */
    public function getDataAnswer()
    {
        $strResponse = "";
        Do
        {
            $str = $this->objComport->ReadString();
            if ($str != "" && $this->objComport->LastError == 0)
                $strResponse = $strResponse . $str;
        }
        While ($str != "");
        return $strResponse;
    }

    /**
     * @return bool
     */
    private function portOpen()
    {

        $this->objComport->Device = "Com".$this->port;
        $this->objComport->Baudrate = 115200;

        $this->objComport->Open();

        return $this->objComport->LastError;

    }


}