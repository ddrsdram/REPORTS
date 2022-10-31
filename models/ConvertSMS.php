<?php

namespace models;

class ConvertSMS
{
    private $PDU;
    private $SMS;

    public $SMS_id;
    public $SMS_telephone;
    public $SMS_date;
    public $SMS_time;
    public $SMS_text;
    private $startPosBodySMS;
    private $quantityAll;
    private $quantityThis;
    public $uIdSMS;
    public $idSMS;
    public $colSMS;


    function __construct()
    {
        //$this->startPosBodySMS = 64; // МЧС
        $this->startPosBodySMS = 54; // Обычная
        $this->startPosBodySMS = 0; // С нуля считываем простые СМС обычного номера

        $this->startPosBodySMS = 14; // Считываем смс которые приходят в нескололько пакетов с коротких номеров
        $this->startPosBodySMS = 12; // Считываем смс которые приходят в нескололько пакетов с коротких номеров
    }

    public function setSMS($SMS)
    {
        $this->SMS = Array();
        $this->SMS = explode(',',$SMS);
        $this->startPosBodySMS = 0;
        if (count($this->SMS) > 3){
            $this->detectId();
            $this->detectTelephone();
            $this->detectDate();
            $this->detectTime();
            $this->detectPDU();
            $this->convertBodySMS();
            $this->detectCompositeSMS();
            return true;
        }else{
            return false;
        }
    }

    public function generateSMS()
    {
        $HeadSMS1="0011000B91";
        $HeadSMS2="0008A7";
        $tel0 = $this->SMS_telephone."F";
        $tel="";
        $i=0;
        while($i<=12){
            $tel = $tel.substr($tel0,$i+1,1).substr($tel0,$i,1);
            $i = $i + 2;
        }
        $text = $this->SMS_text;
        $text = $this->cp1251_2ucs2(mb_convert_encoding($text,'cp-1251','utf-8'));
        $len= strtoupper (dechex(strlen($text)/2));
        $len = strlen($len) == 1 ? "0".$len : $len;
        return $HeadSMS1.$tel.$HeadSMS2.$len.$text;
    }
    function cp1251_2ucs2($str){
        $ucs2="";
        for ($i=0;$i<strlen($str);$i++){
            if (ord($str[$i]) < 127){
                $results = sprintf("%04X",ord($str[$i]));
            }elseif (ord($str[$i])==184){ //ё
                $results="0451";
            }elseif (ord($str[$i])==168){ //Ё
                $results="0401";
            }else{
                $results = sprintf("%04X",(ord($str[$i])-192+1040));
            }
            $ucs2 .= $results;
        }
        return $ucs2;
    }

    private function detectId()
    {
        $this->SMS_id = $this->SMS[0];
    }
    private function detectTelephone()
    {
        $this->SMS_telephone = substr($this->SMS[2],1,mb_strlen($this->SMS[2])-2);
    }
    private function detectDate()
    {
        $date = explode("/",substr($this->SMS[4],1));
        $this->SMS_date = "$date[2]-$date[1]-$date[0]";

    }
    private function detectTime()
    {
        $time = explode('"',$this->SMS[5])[0];
        $time = explode ("+",$time);
        $this->SMS_time = $time[0];
    }
    private function detectPDU()
    {
        $arrPDU=explode('"',$this->SMS[5]);
        $this->PDU = substr($arrPDU[1],2);

    }

    public function detectStartPosBodySMS()
    {
        $this->startPosBodySMS=0;
        switch (substr($this->PDU,0,5)){
            case "06080":
                $this->startPosBodySMS=14;
                break;
            case "05000":
                $this->startPosBodySMS=12;
                break;
        }
    }


    private function convertBodySMS()
    {
        $this->detectStartPosBodySMS();

        $pdu  = mb_substr($this->PDU,$this->startPosBodySMS);
        $pdu  = str_replace(chr(10).chr(13),'',$pdu);
        $pdu  = str_replace(chr(13),'',$pdu);
        $pdu  = str_replace(chr(10),'',$pdu);
        $pdu  = str_replace('OK','',$pdu);
        $pdu = pack("H*",$pdu);
        $this->SMS_text= mb_convert_encoding($pdu,'UTF-8','UCS-2');
    }

    public function detectQuantityAll()
    {
        if ($this->startPosBodySMS == 0){
            return 0;
        }
        else{
            return  (int) substr($this->PDU,8,2) ;
        }
    }

    public function detectCompositeSMS()
    {
        $this->uIdSMS ='';
        $this->idSMS = 0;
        $this->colSMS = 0;

        if ($this->startPosBodySMS == 12){
            $this->uIdSMS = substr($this->PDU,5,3) ;
            $this->idSMS = (int) substr($this->PDU,10,2) ; // порядковый номер в составной СМС
            $this->colSMS = (int) substr($this->PDU,8,2) ; // всего в составной СМС
        }
    }

    public function detectQuantityThis()
    {
        if ($this->startPosBodySMS == 0){
            return 0;
        }
        else{
            return  (int) substr($this->PDU,10,2) ;
        }
    }
}
