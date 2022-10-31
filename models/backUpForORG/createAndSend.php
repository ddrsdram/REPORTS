<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 04.05.2022
 * Time: 11:14
 */

namespace models\backUpForORG;


use Mpdf\Tag\Th;

class createAndSend
{
    private $ORG, $zip, $res;
    private $eMailSendBackUp;
    private $listFileName = Array();
    private $subject = "АИС СеДиАнт - Автоматезированное оповещение";
    private $message;

    public function __construct()
    {
        $this->zip = new \ZipArchive;
        $this->res = $this->zip->open("/var/www/html/downloads/BackUpOrgMonth.zip",\ZIPARCHIVE::CREATE);
        $this->zip->setPassword('rezzalbob');

        print "ZIPPPP <br>$this->res</br>";
        print_r($this->zip);
        print "</br>===";
        $this->initListFileName();
    }


    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @param mixed $eMailSendBackUp
     */
    public function setEMailSendBackUp($eMailSendBackUp)
    {
        $this->eMailSendBackUp = $eMailSendBackUp;
    }

    public function run()
    {

        foreach ($this->listFileName as $tableName=>$whereMonth){
            //$tableName = 'accruals';
            print "create $tableName";
            $fileName = $this->createReportAccrual($tableName,$whereMonth);
            $this->addZipArchive($fileName,$tableName);
        }

        $this->zip->close();

        $this->send();
    }

    private function send()
    {

        $mail = new \models\SendMail();
        $to = $this->eMailSendBackUp;
        $mail->setAddress('tehnosd@mail.ru');
        $mail->setAddress($to);
        $mail->setSubject($this->subject);
        $mail->setContent($this->message);
        $mail->setAttachFile('/var/www/html/downloads/BackUpOrgMonth.zip');
        $mail->send();
        print "<b> send</b>";
    }

    private function createReportAccrual($tableName,$whereMonth)
    {
        $CF = new \models\backUpForORG\CreateFile();
        $CF->setTableName($tableName);
        $CF->setWhereMonth($whereMonth);
        return $CF->create('\\Reports\\backUpForORG');

    }

    private function addZipArchive($fileName,$visibleName)
    {
        $fileName = "/var/www/html/ImpExp/$fileName";
        print "<br>$fileName</br>";
        $this->zip->addFile($fileName, $visibleName.'.txt');
       // unlink ($fileName);
    }

    private function initListFileName()
    {
        $this->listFileName = Array(
            "LS_head"=>"1",
            "LS_owners"=>"1",
            "LS_settings"=>"1",
            "payment"=>"1",
            "recalculation"=>"1",
            "organization"=>"0",
            "device"=>"0",
            "Averaging_value_device_byMonth"=>"1",
            "FIO"=>"1",
            "tarif"=>"1",
            "accruals"=>"1"
        );
    }
}