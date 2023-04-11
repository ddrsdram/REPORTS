<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 23.09.2021
 * Time: 15:57
 */

namespace Views\HTML;


class MSG_MonthIsNotClosed extends MSG
{




    public function getMessage()
    {
        // TODO: Implement getMessage() method.
        $message =
            <<<HTML
<div style="font-family: Arial">
<div style="padding-top:20px; padding-bottom: 20px;padding-left: 60px;background-color: #002697; color: #ffffff">
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
На данный момент предыдущий период не закрыт для внесения изменений,</br>
влияющих на формировния сумм начисления для населения.</br>
В этой связи нет возможности расчитать объем вознаграждения согласно договора №{$this->Contract_num} от {$this->Contract_date}г.
</br>
В случае возникновения сложностей, свяжитесь с нашим специалистом. </br>
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
<div style="padding-top:20px; padding-bottom: 20px;padding-left: 60px;background-color: #002697; color: #ffffff">
</div>
HTML;
        return $message;
    }
}