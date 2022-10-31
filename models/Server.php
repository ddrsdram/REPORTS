<?php
namespace models;
use mysql_xdevapi\Exception;

class Server
{
    /**
     * @var Modem3 \Modem3
     */
    private $modem;

    function __construct()
    {
        $this->modem = new Modem3();
        $this->modem->setPort("1");
        $this->modem->connect();
    }

    public function readSMS()
    {
        print "start";
        print "</br>".chr(10).chr(13);

        $convertSMS = new ConvertSMS();
        $conn = new \backend\Connection();


        $this->modem->writeData("at+csq");
        $this->modem->getDataAnswer();


        $this->modem->writeData("ATZ");
        $this->modem->getDataAnswer();
        $this->modem->writeData("ATE1" );
        $this->modem->getDataAnswer();


        print "Write AT+CMGF=1 ".$this->modem->writeData("AT+CMGF=1");
        $this->modem->getDataAnswer();
        print "</br>".chr(10).chr(13);

        $this->modem->writeData("AT+CMGL=\"ALL\"",1);
        $allSMS = $this->modem->getDataAnswer();
        print "&&&&&&&&&&&&&".$allSMS."</br>".chr(10).chr(13);
        $allSMS= mb_substr($allSMS,0,mb_strlen($allSMS)-2);

        $arrAllSMS=explode('+CMGL:',$allSMS);
        if (count ($arrAllSMS)>1){
            $deleteArray = Array();// массив для удаления СМС после получения
            foreach ($arrAllSMS as $key => $value){
                if ($convertSMS->setSMS($value)){
                    print "</br>".chr(10).chr(13).$value;
                    print "</br>======================</br>";
                    print $convertSMS->SMS_id."</br>".chr(10).chr(13);
                    print $convertSMS->SMS_date."</br>".chr(10).chr(13);
                    print $convertSMS->SMS_telephone."</br>".chr(10).chr(13);
                    print $convertSMS->SMS_time."</br>".chr(10).chr(13);
                    print $convertSMS->SMS_text."</br>".chr(10).chr(13);
                    if(mb_substr($convertSMS->SMS_id,0,13) != 'AT+CMGL="ALL"'){
                        $res=$conn->table("SMS_receiving")
                            ->set('SMS_RAW',$value)
                            ->insert();
                        try {
                            $conn->table("SMS_receiving")
                                ->set("SMS_date",$convertSMS->SMS_date)
                                ->set("SMS_time",$convertSMS->SMS_time)
                                ->set("SMS_telephone",$convertSMS->SMS_telephone)
                                ->set("SMS_text",$convertSMS->SMS_text)
                                ->set("uIdSMS",$convertSMS->uIdSMS)
                                ->set("idSMS",$convertSMS->idSMS)
                                ->set("colSMS",$convertSMS->colSMS)

                                ->where('id',$res)
                                ->update();
                            $deleteArray[] = $convertSMS->SMS_id;// заполение массива для удаления СМС после получения
                        }catch (PDOException $e){

                        }

                    }
                    print "</br>======================</br>";
                }
            }

            // Удаления обработанных СМС
            foreach ($deleteArray as $key => $id_SMS ){
                $this->modem->writeData("AT+CMGd=".$id_SMS);
                print "</br> del SMS = ".$this->modem->getDataAnswer();
            }


        }else{
            print "</br>========== НЕТ Воходящих сообщений ============</br>";
        }

    }

    public function SendSMS()
    {


        $this->modem->getDataAnswer();
        print "</br>".chr(10).chr(13);
        print "</br>".chr(10).chr(13);

        $conn = new \backend\Connection(\backend\Connection::GD);

        $SMS_send_all=$conn->table("SMS_send")
            ->where("f_exec",'0')
            ->select("id,number_tel,message")
            ->fetchAll();
        print_r($SMS_send_all);
        print "</br></br>";
        print "</br></br>";
        print "</br></br>";
        print "</br></br>";
        foreach ($SMS_send_all as $key => $res){
            $id = $res['id'];
            $convertSMS = new ConvertSMS();

            print "Write AT+CMGF=0 ".$this->modem->writeData("AT+CMGF=0" );
            $convertSMS->SMS_telephone = trim($res['number_tel']);
            $convertSMS->SMS_text = trim($res['message']);
            print "tel:$convertSMS->SMS_telephone => msg:$convertSMS->SMS_text ";
            print "</br>".chr(10).chr(13);

            if (!((mb_strlen($convertSMS->SMS_telephone)==0) or (mb_strlen($convertSMS->SMS_text)==0))){
                $dataForSend=$convertSMS->generateSMS();


                $this->modem->writeData("AT+CMGS=".((mb_strlen($dataForSend)/2)-1),1);
                //print "</br>AT+CMGS = ".((mb_strlen($dataForSend)/2)-1)." res->".$this->modem->getDataAnswer();
                $this->modem->writeData($dataForSend.chr(26));
                print "</br>AT+CMGS = ".$this->modem->getDataAnswer();

            }
            print "</br>========================================================</br>";
            print "</br></br>";
            $conn->table("SMS_send")
                ->set("f_exec",'1')
                ->where("id",$id)
                ->update();
            sleep(4);
        }

    }
}
