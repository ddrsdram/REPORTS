<?php


namespace models;


use PhpOffice\PhpSpreadsheet\Calculation\Engine\ArrayArgumentHelper;

class SendUPD
{
    private int $ORG;
    private int $id_month;
    private bool $sendMailMyOnly = true;


    /**
     * @param int $id_month
     */
    public function setIdMonth(int $id_month): void
    {
        $this->id_month = $id_month;
    }

    /**
     * @param int $ORG
     */
    public function setORG(int $ORG): void
    {
        $this->ORG = $ORG;
    }


    /**
     * @param bool $sendMailMyOnly
     */
    public function setSendMailMyOnly(bool $sendMailMyOnly): void
    {
        $this->sendMailMyOnly = $sendMailMyOnly;
    }


    private function getRequisites() :Array
    {
        $conn = new \DB\Connect(\backend\Connection::GD);
        return $conn->table('View_S_B_K_UniversalPaymentDocument_ForSend')
            ->where("ORG",$this->ORG)
            ->where("id_month",$this->id_month)
            ->select()->fetch();
    }


    public function send()
    {
        $res = $this->getRequisites();
        $_SESSION['id_month0'] = $res['id_month'];
        $_SESSION['ORG'] = $res['ORG'];
        $_SESSION['id_user'] = '0';// пользователь нужен но не важен
        // Сформировать файл с по
        $res['Contract_date'] = date('d.m.Y',strtotime($res['Contract_date']));
        $res['invoicedAamount'] = round($res['invoicedAamount'],2);
        $UPD = new \models\UPD();
        $UPD->setIdUPD($res['id']);
        $UPD->setORG($res['ORG']);
        $UPD->setSum($res['invoicedAamount']);
        $UPD->setNameAIS($res['name_AIS']);
        $UPD->setContractNum($res['Contract_num']);
        $UPD->setContractDate($res['Contract_date']);
        $filename  = $UPD->create();
        $security = new \properties\security();
        $dir = $security->DIR();
        unlink($dir."/download/Универсальный_передаточный_документ.pdf");
        rename($dir."/download/$filename",$dir."/download/Универсальный_передаточный_документ.pdf");


        $fio =  $res['IO'];
        $MSG = new \Views\HTML\Msg_sendUPD($fio);
        $MSG->setSum($res['invoicedAamount']);
        $MSG->setNameAIS($res['name_AIS']);
        $MSG->setContractNum($res['Contract_num']);
        $MSG->setContractDate($res['Contract_date']);
        $message = $MSG->getMessage();
        print $message;

        $subject = "АИС СеДиАнт - Автоматезированное оповещение";

        $mail = new \models\SendMail();
        $mail->setAddress('tehnosd@mail.ru');
        if ($this->sendMailMyOnly === false)
            $mail->setAddress($res['eMail']);
        $mail->setSubject($subject);
        $mail->setContent($message);
        $mail->setAttachFile($dir."/download/Универсальный_передаточный_документ.pdf");
        $mail->send();
        unset($mail);

    }
}