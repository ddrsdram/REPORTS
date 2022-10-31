<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 23.09.2021
 * Time: 15:57
 */

namespace Views\HTML;


class Msg_missedPayment extends MSG
{

    public function getMessage()
    {
        // TODO: Implement getMessage() method.
        $message =
            <<<HTML
<div style="font-family: Arial">
<div style="padding-top:20px; padding-bottom: 20px;padding-left: 60px;background-color: #ffd8dc; color: #000">
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
<b style="padding-left: 30px">Вынуждены Вас проинформировать о следующем:</b> </br>
На данный момент нет поступлений средств от Вашей организации,</br>
согласно договора  №{$this->Contract_num} от {$this->Contract_date}г.</br> 
о предоставлении доступа к системе "{$this->nameAIS}" п.4.5.</br>
Универсальный передаточный документ на сумму {$this->sum} рублей был выслан ранее.  </br>
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
<div style="padding-top:20px; padding-bottom: 20px;padding-left: 60px;background-color: #ffd8dc; color: #000">
</div>
HTML;
        return $message;
    }
}