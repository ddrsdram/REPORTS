<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\Tribunal\StatementOfClaim;


use models\Reports;

class VIEW extends \Reports\reportView
{
    private $SF_bold = Array();
    private $SF_Norm = Array();
    private $SF_Yell = Array();
    private $style2 = Array();


    /**
     * @var \Reports\Tribunal\StatementOfClaim\MODEL
     */
    private $MODEL;

    function init()
    {
        $path = $_SERVER['DOCUMENT_ROOT'];
        $this->resultFilePath = $path."/ImpExp";
        $this->objectDoc = new \PhpOffice\PhpWord\PhpWord();

        $this->style2 = array(
            'indentation' => array ('firstLine' => 700),
            'align'=>'both',
            'LineSpacingRule' => 'exact',
            'spacing' => 0,
            'spaceBefore' => 0,
            'spaceAfter' => 0,
        );

        $this->SF_bold = array(
            'bold'=>true
        );
        $this->SF_Norm = array(
            'bold'=>false);

        $this->SF_Yell = array(
            'bold'=>false,
            'bgColor' => 'ffffff'
        );

    }


    public function setMODEL($MODEL)
    {
        $this->MODEL = $MODEL;
    }


    public function saveFile()
    {

        $fileName = "$this->resultFilePath/$this->resultFileName$this->extensionName";
        unlink($fileName);
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($this->objectDoc, 'Word2007');
        $objWriter->save($fileName);

    }

    public function FillingInData()
    {
        $this->objectDoc->setDefaultFontName('Times New Roman');
        $this->objectDoc->setDefaultFontSize(12);

        $sectionStyle = array(
            'orientation' => null,        //задали портретную ориентацию
            'marginLeft' => 1200,                 //отступ слева на 900 твипов
            'marginRight' => 900,               //отступ справа
            'marginTop' => 900,                //отступ сверху
            'marginBottom' => 900,             //отступ снизу
            'space' => array('before' => 0, 'after' => 0),
            //'indentation' => array('left' => 540, 'right' => 120)
            );
        $this->section = $this->objectDoc->addSection($sectionStyle);
        $this->addHeadText('Мировому судье судебного участка № 2 Беловского городского судебного района Кемеровской области',true);
//        $this->addHeadText('',true);
        $this->addHeadText('Истец:',true);
        $this->addHeadText($this->H['name_organization']);
        $this->addHeadText('ИНН:'.$this->H['INN']);
        $this->addHeadText('ОГРН:'.$this->H['OGRN']);
        $this->addHeadText('Адрес:');
        $this->addHeadText($this->H['address']);
        $this->addHeadText('представитель Истца: Безматерных Андрей Викторович');
        $this->addHeadText('тел. 8-903-916-76-00');

        $this->addHeadText('Ответчик:');
        $this->addPeopleInToHeader();
        // перечень людей для суда
        $this->addHeadText('Адрес:');
        $this->addHeadText($this->H['addressHouse']);
        $this->addHeadText("Сумма задолженности: ".$this->H['accrual']."р.",true);
        if ($this->H['PenaltyOff'] == 1){
            $this->addHeadText("Сумма пени " . $this->H['SumPenalty'] . " р.",true);
        }
        $this->addHeadText("Госпошлина: ".$this->H['GosPoshlina']."р.",true);

        $this->addCenterText("");
        $this->addCenterText("");
        $this->addCenterText("");
        $this->addCenterText("Исковое заявление");
        $this->addCenterText('о взыскании задолженности за содержание жилого помещения');
        $this->addCenterText("");
        $this->addText("По адресу: ".$this->H['addressHouse']." зарегистрированы и проживают:",false,true);

        $this->addPeopleInToBody();

        $this->addText("Решением собственников помещений в многоквартирном доме № {$this->H['house']} ".
            "{$this->H['status_street']} {$this->H['name_street']} (Протокол{$this->H['N_protocol']} от {$this->H['D_protocol']} г.) выбран способ управления многоквартирным домом в форме управления Управляющей организацией ООО «Жилсервис»");
        $this->addText("Истец управляет и обеспечивает содержание, текущий ремонт и эксплуатацию имущества указанного жилого дома по {$this->H['status_street']} {$this->H['name_street']}, д.{$this->H['house']}, где проживают Ответчики.");
        $this->addText("Размер платы за содержание и ремонт жилого помещения в многоквартирном доме в соответствии с частью 4 статьи 158 ЖК РФ устанавливают органы местного самоуправления в случае, если собственники помещений в многоквартирном доме на общем собрании не приняли решение об установлении размера платы за содержание и ремонт жилого помещения.");
        $this->addText("Порядок расчета, цены, ставки и тарифы на жилищно-коммунальные услуги для населения Беловского городского округа устанавливаются Советом народных депутатов Беловского городского округа, что является основанием для начисления оплаты жилищно-коммунальных услуг.");
        $this->addText("В соответствии со статьей 39 Жилищного Кодекса РФ собственники помещений в многоквартирном доме несут бремя расходов на содержание общего имущества в многоквартирном доме.");
        $this->addText("Ст. 153 Жилищного Кодекса РФ обязывает граждан и организации своевременно и полностью вносить плату за жилое помещение и коммунальные услуги. В соответствии со статьей 154 Жилищного Кодекса РФ плата за жилое помещение и коммунальные услуги для собственника помещения в многоквартирном доме включает в себя: 1) плату за содержание и ремонт жилого помещения, включающую в себя плату за услуги и работы по управлению многоквартирным домом, содержанию, текущему ремонту общего имущества в многоквартирном доме.");
        $this->addText("Согласно статьи 155 Жилищного Кодекса РФ плата за жилое помещение и коммунальные услуги вносится ежемесячно до десятого числа месяца, следующего за истекшим месяцем.");
        $this->addText("Частью 3 статьи 30 Жилищного кодекса РФ предусмотрено, что собственник жилого помещения несет бремя содержания данного помещения. Данная норма базируется на положениях ст. 210 Гражданского кодекса РФ, которой установлено, что собственник несет бремя содержания, принадлежащего ему имущества.");
        $this->addText("Свои обязанности, установленные ст.ст. 153-155 ЖК РФ по своевременной и полной оплате за жилое помещение Ответчик не исполняет.");
        $this->addText("Каких-либо возражений и вопросов от Ответчика по начислению оплаты за содержание жилья и текущий ремонт в адрес Истца не поступало.");

        $txt_accrual = \models\Num2Str::getText($this->H['accrual']);
        $this->addText("Общая задолженность за содержание жилья и текущий ремонт за период с " . $this->H['dateStart'] . "г. по " . $this->H['dateEnd'] . "г. " .
        "составляет {$this->H['accrual']} ($txt_accrual).");
        $this->addText("Согласно ст. 309 ГК РФ обязательства должны исполняться надлежащим образом в соответствии с условиями обязательства и требованиями закона. Указанные требования Ответчики не исполняют.");
        $this->addText("До настоящего момента Ответчиками не предприняты меры по погашению образовавшейся  задолженности, что послужило основанием для обращения с настоящим исковым заявлением.");
        $this->addText("На основании изложенного, руководствуясь ст. 309, 310 ГК РФ и ст. 3, 22, 23, 131, 132, 151 ГПК РФ, ст.ст. 153-155 ЖК РФ,");
        $this->addText("");






        $this->addText("ПРОШУ:");

        $textRun = $this->section->createTextRun($this->style2);


        $text = "1. Взыскать солидарно с ".$this->H['FIOSumm']." проживающих по адресу: ".$this->H['addressHouse']." в пользу " ;
        $text .= $this->H['name_organization_full'] ;
        $text .= " (ИНН ".$this->H['INN'] ;
        $text .= "/ КПП ".$this->H['KKP'] ;
        $text .= ") Р/с ".$this->H['RSCH'] ;
        $text .= " в ".$this->H['name_bank'] ;
        $text .= ",  К/с ".$this->H['KSCH'] ;
        $text .= ", БИК ".$this->H['BIK'] ;
        $text .= ")  долг за содержание жилья и текущий ремонт за период с " . $this->H['dateStart'] . "г. по " . $this->H['dateEnd'] . "г. в сумме ";
        $textRun->addText($text,$this->SF_Norm);
        $text =  $this->H['accrual'] . " рублей., " ;

        $textRun->addText($text,$this->SF_Norm);
        $sum = $this->H['accrual'];
        if ($this->H['PenaltyOff'] == 1){
            $textRun->addText(" сумму пени ",$this->SF_Norm);
            $textRun->addText($this->H['SumPenalty'] . " руб.",$this->SF_Norm);
            $sum = $sum+ $this->H['SumPenalty'];
        }
        $text = "  и расходы по уплате государственной пошлины в размере ";
        $textRun->addText($text,$this->SF_Norm);
        $textRun->addText($this->H['GosPoshlina'] ." руб.",$this->SF_Norm);

        $sum = $sum+ $this->H['GosPoshlina'];
        $txt_accrual = \models\Num2Str::getText($sum);

        $textRun->addText(", всего ",$this->SF_Norm);
        $text =  $this->H['summa'] ." (". $txt_accrual .")";
        $textRun->addText($text,$this->SF_bold);


        $this->addText("2. Ходатайство: произвести зачет ранее уплаченной Истцом при подаче заявления о вынесении судебного приказа государственной пошлины в счет уплаты государственной пошлины за рассмотрение настоящего искового заявления к Ответчику о взыскании задолженности за содержание жилого помещения в связи с отменой судебного приказа (ст. 333.22 НК РФ).");

        $textRun = $this->section->createTextRun($this->style2);
        $textRun->addText("3. Ходатайство: произвести зачет госпошлины в размере ",$this->SF_Norm);
        $textRun->addText("XXXX рублей ",$this->SF_Yell);
        $textRun->addText(", уплаченных ООО «Жилсервис» ",$this->SF_Norm);
        $textRun->addText("22.09.2022",$this->SF_Yell);
        $textRun->addText(". согласно платежному поручению № 544. Заявление о возврате госпошлины не подавалось. В соответствии с правилами ст. 333.40 Налогового кодекса плательщик госпошлины вправе произвести излишне уплаченные суммы в счет суммы госпошлины, подлежащей уплате за совершение аналогичного действия.",$this->SF_Norm);

        $this->addText("");


        $this->addText("Приложение:");
        $this->addText("1. Платежное поручение об оплате государственной пошлины от 22.09.2022 № 544;",false,true);
        $this->addText("2. Платежное поручение об оплате государственной пошлины от 09.11.2022 № 641;",false,true);
        $this->addText("3. Копия почтовой квитанции о вручении искового заявления Ответчику на 2-х листах;",false,true);
        $this->addText("4. Расчет задолженности.",false,true);
        $this->addText("5. Начисления по лицевому счету  по оплате за содержание жилья и текущий ремонт на 1-м листе;",false,true);
        $this->addText("6.  Выписка из ЕГРЮЛ на 5-ти листах; ",false,true);


        $this->addText("7. Копия протокола проведения общего собрания собственников в многоквартирном доме № {$this->H['house']}, расположенному по адресу: {$this->H['status_street']} {$this->H['name_street']} (заочная форма) {$this->H['N_protocol']} от {$this->H['D_protocol']}г.;",false,true);
        $this->addText("8. Справка с места жительства;",false,true);
        $this->addText("9. Решение СНД БГО от 30.05.2019 № 9/41-н  на 1-м  листе;",false,true);
        $this->addText("10. Решение СНД БГО от 30.07.2020  № 24/140-н на 1-м  листе;",false,true);
        $this->addText("11. Решение СНД БГО от 22.09.2021 № 40/219-н  на 1-м  листе;",false,true);
        $this->addText("12. Копия определения об отмене судебного приказа от 25.10.2022г. по делу  №2-1739/2022;",false,true);
        if ($this->H['PenaltyOff'] == 1){
            $this->addText("13.Расчет пени.",false,true);
        }

        $this->addText("");
        $this->addText("");
        $this->addText("");
        $this->addText(date('d.m.Y')."                                                                                            Генеральный директор",false,Array('align'=>'right'));
        $this->addText("");
        $this->addText("___________________ А.В. Безматерных",false,Array('align'=>'right'));

        $this->addText("");

    }


    private function addPeopleInToHeader()
    {
        foreach ($this->data as  $key => $t1){
            $this->addHeadText($t1['FIO'].' '.date('d.m.Y',strtotime($t1['birthday'])).' года рождения');
        }
    }

    private function addPeopleInToBody()
    {
        foreach ($this->data as  $key => $t2){
            $this->addText("- ".$t2['FIO']." ".$t2['birthday']. " г.р. ".$t2['native']." ".$t2['caption'],true,true);
        }
    }
    private function addHeadText($text,$bold = false)
    {
        $this->section->addText(
            $text,
            array('bold' => $bold,
                'spaceBefore' => 0,
                'spaceAfter' => 0),
            array('indentation' => array('left' => 5000, 'right' => 0),
                'spaceBefore' => 0,
                'spaceAfter' => 0)
        );

    }

    private function addCenterText($text,$bold = true)
    {
        $this->section->addText(
            $text,
            array('bold' => $bold),
            array(
                'spaceBefore' => 0,'spaceAfter' => 0,
                'align'=>'center'
            )
        );

    }

    private function addText($text,$bold = false,$spaceAfterBefore_is0 = false)
    {
        $style2 = array(
            'indentation' => array ('firstLine' => 700),
            'align'=>'both',
            'LineSpacingRule' => 'exact',
            'spacing' => 0,
            'spaceBefore' => 0,
            'spaceAfter' => 0,
        );
        if ($spaceAfterBefore_is0){
            $style2['spaceBefore'] = 0;
            $style2['spaceAfter'] = 0;
            if (is_array($spaceAfterBefore_is0)){
                foreach ($spaceAfterBefore_is0 as $key => $value){
                    $style2[$key] = $value;
                }
            }
        }
        if ($bold === false){
            $bold = array('bold' => false);
        }
        $this->section->addText(
            $text,
            $bold,
            $style2
        );

    }
}