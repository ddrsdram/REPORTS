<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 21.12.2021
 * Time: 12:12
 */

namespace models;


class SendMail
{
    private $address = 'tehnosd@mail.ru';
    private $addressCopy = 'tehnosd@mail.ru';
    private $content = 'Тестовое Сообщение';
    private $attachFile = false;
    private $subject = 'Темя не задана';


    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }


    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @param string $addressCopy
     */
    public function setAddressCopy($addressCopy)
    {
        $this->addressCopy = $addressCopy;
    }


    /**
     * @param string $attachFile
     */
    public function setAttachFile($attachFile)
    {
        $this->attachFile = $attachFile;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    public function send()
    {
        date_default_timezone_set('Etc/UTC');
        $mail = new \PHPMailer\PHPMailer();
        $mail->CharSet = 'UTF-8';
//$mail->SMTPDebug = 1;            // Если что-то не работает
        $mail->isSMTP();
        $mail->Host = 'smtp.yandex.ru';
        $mail->SMTPAuth = true;
        $mail->Username = 'sediant@yandex.ru';    // Логин от ящика
        $mail->Password = 'kdnldcvlptrejaye';     // Пароль
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        $mail->From = 'sediant@yandex.ru';        // От кого будет письмо
        $mail->FromName = 'Автоматезированная информационная система СеДиАнт';        // Как зовут "от кого"
        $mail->addAddress($this->address);         // Куда посылать
        if ($this->addressCopy != $this->address){
            $mail->addAddress($this->addressCopy);         // Куда посылать копию
        }

        $mail->isHTML(true);
        $mail->Subject = $this->subject;          // Тема
        $mail->Body = $this->content ;

        if ($this->attachFile !== false){
            $mail->addAttachment($this->attachFile);
        }

        $mail->send();


        unset($mail);
    }

}