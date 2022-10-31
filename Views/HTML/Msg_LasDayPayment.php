<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 23.09.2021
 * Time: 15:57
 */

namespace Views\HTML;


class Msg_LasDayPayment extends MSG
{

    public function getMessage()
    {
        // TODO: Implement getMessage() method.
        $message =
            <<<HTML
<div style="font-family: Arial">
<div style="padding-top:20px; padding-bottom: 20px;padding-left: 60px;background-color: #ffdf5c; color: #000">
Данное письмо сформировано автоматизированной информационной  системой "{$this->nameAIS}"
</br>
Не отвечайте на него!
</div>

<div style="padding-top:20px; padding-bottom: 20px;padding-left: 60px;background-color: #7ab8ff; color: #ffffff">
</br>
<b>{$this->gender} {$this->FIO}!</b></br>
</br>
</div>
<div style="padding-top:20px; padding-bottom: 20px;padding-left: 60px">
</br>
</br>
<b style="padding-left: 30px">Информируем Вас о наступлении крайнего срока перечисления средств за использование системы.</br>
</b> </br>
Согласно договора  №{$this->Contract_num} от {$this->Contract_date}г.</br> 
о предоставлении доступа к системе "{$this->nameAIS}" </br>
</br>
п. 3.3.1 Оплачивать доступ к Системе на условиях раздела 4 настоящего Договора </br>
с учётом положений изложенных в Тарифной политики, являющейся приложением к настоящему Договору.</br>
</br>
п. 4.5.	Партнёр производит очередную оплату не позднее 20 (Двадцатого) числа месяца </br>
за пользование системой за предыдущий учетный период. </br>
</br>
Ожидаем оплаты согласно высланного ранее </br>
универсального передаточного документа на сумму {$this->sum} рублей.  </br>
</br>
</br>
Для уточнения деталей свяжитесь с нашим специалистом. </br>
</br>
</br>
</br>
</br>
</div>
<div style="padding-top:20px; padding-bottom: 20px;padding-left: 60px;background-color: #7ab8ff; color: #ffffff">
Ещё раз обращаем Ваше внимание на то, что письмо носит информационный характер.</br>
Отвечать на него не нужно!
</div>
<div style="padding-top:20px; padding-bottom: 20px;padding-left: 60px">
</br>
</br>
<b>С Уважением!</b> </br>
И.П. Серякова Н.А</br>
тел. 8-923-494-27-72</br>
eMail tehnosd@mail.ru</br>
</div>
</div>
<div style="padding-top:20px; padding-bottom: 20px;padding-left: 60px;background-color: #ffdf5c; color: #000">
</div>
HTML;
        return $message;
    }
}