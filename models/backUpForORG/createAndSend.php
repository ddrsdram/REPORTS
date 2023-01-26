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

    private $root;

    private $connection_array = Array();

    public function __construct()
    {
        $this->root = \properties\security::DOCUMENT_ROOT_PATH;

        $this->zip = new \ZipArchive;

        $this->res = $this->zip->open("$this->root/download/BackUpOrgMonth.zip",\ZIPARCHIVE::CREATE);
        $this->zip->setPassword('rezzalbob');

        print "ZIPPPP <br>$this->res</br>".chr(10).chr(13);
        print_r($this->zip);
        print "</br>===".chr(10).chr(13);
        $this->initListFileName();
    }

    /**
     * @param array $connection_array
     */
    public function setConnectionArray(array $connection_array)
    {
        $this->connection_array = $connection_array;
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
            print "create $tableName".chr(10).chr(13);
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
        $mail->setAttachFile("$this->root/download/BackUpOrgMonth.zip");
        $mail->send();
        print "<b> send</b>".chr(10).chr(13);
    }

    private function createReportAccrual($tableName,$whereMonth)
    {
        $CF = new \models\backUpForORG\CreateFile();
        $CF->setTableName($tableName);
        $CF->setWhereMonth($whereMonth);
        $CF->setConnectionArray($this->connection_array);

        return $CF->create('\\Reports\\backUpForORG');

    }

    private function addZipArchive($fileName,$visibleName)
    {
        $fileName = "$this->root/ImpExp/$fileName";
        print "<br>$fileName</br>".chr(10).chr(13);
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