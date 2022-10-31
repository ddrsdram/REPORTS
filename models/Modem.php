<?php

/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 13.09.2019
 * Time: 16:54
 */
namespace models;

class Modem
{
    private $flagOpenPort;
    private $port;

    function __construct()
    {
        $this->flagOpenPort=false;
        $this->port=false;
        ser_register("Dmitry Seryakov #1","1673061565");
    }

    function __destruct()
    {
        // TODO: Implement __destruct() method.
        $this->portClose();
        print "</br>Close PORT</br>";
    }

    /**
     * @param $port
     * @return $this
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @return bool
     */
    public function connect()
    {

           if ($this->port!=false)
            if (!$this->portIsOpen())
                $this->flagOpenPort=$this->portOpen();
        sleep(1);
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
        $res=ser_write($textData."\r");
        sleep(5);
        return $res;
    }

    /**
     * @return mixed
     */
    public function getDataAnswer()
    {

        $res = ser_read(15000);

        return $res;
    }

    /**
     * @return bool
     */
    private function portOpen()
    {
        return ser_open("COM".$this->port, 115200, 8, "None", "1", "None");
        sleep(1);

    }


    /**
     * @return bool
     */
    private function portIsOpen()
    {
        return ser_isopen();
    }

    /**
     * @return bool
     */
    private function portClose()
    {
        return ser_close();
    }

    public function ser_version()
    {
        return ser_version();
    }
}