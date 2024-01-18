<?php
/**
 * Created by PhpStorm.
 * User: rezzalbob
 * Date: 20.04.2020
 * Time: 16:00
 */

namespace Reports\Tribunal\StatementTribunalOrder;


class VIEW extends \Reports\reportView
{

    function init()
    {
        $path = $_SERVER['DOCUMENT_ROOT'];
        $this->resultFilePath = $path."/ImpExp";
        $this->objectDoc = new \PhpOffice\PhpWord\PhpWord();
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

        $sectionStyle = array('orientation' => null,        //задали портретную ориентацию
            'marginLeft' => 1200,                 //отступ слева на 900 твипов
            'marginRight' => 900,               //отступ справа
            'marginTop' => 900,                //отступ сверху
            'marginBottom' => 900,
            );          //отступ снизу
        $this->section = $this->objectDoc->addSection($sectionStyle);
        $this->addHeadText('Мировому судье судебного участка № 2 Беловского городского судебного района Кемеровской области',true);
//        $this->addHeadText('',true);
        $this->addHeadText('Заявитель:',true);
        $this->addHeadText($this->H['name_organization']);
        $this->addHeadText('Адрес:');
        $this->addHeadText($this->H['address']);
        $this->addHeadText('Должник:');
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
        $this->addCenterText("ЗАЯВЛЕНИЕ");
        $this->addCenterText('О ВЫДАЧЕ СУДЕБНОГО ПРИКАЗА');
        $this->addCenterText("");
        $this->addText("По адресу: ".$this->H['addressHouse']." зарегистрированы и проживают:",false,true);

        $this->addPeopleInToBody();

        $this->addText("Свои обязанности, установленные ст.ст. 153-155 ЖК РФ и ст. 544 ГК РФ по своевременной и полной оплате содержания жилья, не исполняют.");
        $this->addText("С ". $this->H['dateStart'] ."года по ". $this->H['dateEnd'] ."года, за должниками образовалась задолженность по оплате предоставленных услуг по содержанию жилья и текущему ремонту общего имущества жилого дома  в размере ". $this->H['accrual'] ."  руб.");
        $this->addText("Никаких возражений и вопросов по начислению оплаты за услуги по содержанию и текущему ремонту в адрес взыскателя не поступало.");
        $this->addText("Согласно Жилищного кодекса РФ Раздел 7 ст.155 п.1 «собственник, наниматели  иные уполномоченные граждане, занимающие жилое помещение обязаны осуществлять оплату предоставляемых им коммунальных услуг» плата за жилое помещение и коммунальные услуги вносится ежемесячно до десятого (10) числа месяца, следующего за истекшим месяцем».");
        $this->addText("Согласно ст. 309 ГК РФ «обязательства должны исполняться надлежащим образом в соответствии с условиями обязательства и требованиями закона», ст. 310 ГК РФ \"односторонний отказ от исполнения обязательств и одностороннее изменение его условий не допускается\".");
        $this->addText("Никаких  мер по погашению данной задолженности должники не принимают. ".$this->H['name_organization']." должникам была направлена претензия с просьбой погасить сумму задолженности.");
        $this->addText("На основании изложенного и руководствуясь ст.309, 310, 539, 544 ГК РФ и ст. 3, 22,23, ГПК РФ, ст. 131,132,151 ГПК РФ, ст.ст.153-155, п.2 ст.69 ЖК РФ;");
        $this->addText("ПРОШУ:");
        $this->addText("Выдать судебный приказ о взыскании солидарно с  должников:");

        $text = "- ".$this->H['FIOSumm']." проживающих по адресу: ".$this->H['addressHouse']." в пользу " ;
        $text .= $this->H['name_organization_full'] ;
        $text .= " (ИНН ".$this->H['INN'] ;
        $text .= "/ КПП ".$this->H['KKP'] ;
        $text .= ") Р/с ".$this->H['RSCH'] ;
        $text .= " в ".$this->H['name_bank'] ;
        $text .= ",  К/с ".$this->H['KSCH'] ;
        $text .= ", БИК ".$this->H['BIK'] ;
        $text .= ")  долг за содержание жилья  в размере " . $this->H['accrual'] . " руб.," ;
        if ($this->H['PenaltyOff'] == 1){
            $text .= " сумму пени " . $this->H['SumPenalty'] . " руб." ;
        }
        $text .= "  и расходы по уплате государственной пошлины в размере ". $this->H['GosPoshlina'] ." руб." ;
        $text .= ", всего ". $this->H['summa'] ." (". $this->H['text'] .")";
        $this->addText($text);
        $this->addText("");

        $this->addText("Приложение:",false,true);
        $this->addText("1.Квитанция об оплате госпошлины.",false,true);
        $this->addText("2.Справка из паспортного стола о составе семьи.",false,true);
        $this->addText("3.Копия лицевого счета (карточка собственника) должника с расчетом задолженности по оплате за содержание жилья.",false,true);
        if ($this->H['PenaltyOff'] == 1){
            $this->addText("4.Расчет пени.",false,true);
        }

        $this->addText("");
        $this->addText("");
        $this->addText("Представитель ",false,true);
        $this->addText("ООО «Жилсервис»                                    __________________   /О.М. Самойлова/",false,true);
        $this->addText("(по доверенности) ",false,true);
        $this->addText("");

    }


    private function addPeopleInToHeader()
    {
        foreach ($this->data as  $key => $t1){
            $this->addHeadText($t1['FIO'],true);
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

        );
        if ($spaceAfterBefore_is0){
            $style2['spaceBefore'] = 0;
            $style2['spaceAfter'] = 0;
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