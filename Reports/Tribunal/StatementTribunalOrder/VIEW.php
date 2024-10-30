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
    private $SF_bold = Array();
    private $SF_Norm = Array();
    private $SF_Yell = Array();
    private $style2 = Array();
    private $style3 = Array();
    private $countOfHuman;
    const Left = 2000;
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

        $this->style3 = array(

            'indentation' => array ('left' => self::Left,'firstLine' => 0),
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


    public function saveFile()
    {

        $fileName = "$this->resultFilePath/$this->resultFileName$this->extensionName";
        unlink($fileName);
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($this->objectDoc, 'Word2007');
        $objWriter->save($fileName);

    }

    public function FillingInData()
    {

        $settings = $this->MODEL->getSettingsTribunal();
        $AJusticeOfThePeace = $settings['AJusticeOfThePeace'];
        $Signatory = $settings['Signatory'];


        $this->objectDoc->setDefaultFontName('Times New Roman');
        $this->objectDoc->setDefaultFontSize(12);

        $sectionStyle = array('orientation' => null,        //задали портретную ориентацию
            'marginLeft' => 1200,                 //отступ слева на 900 твипов
            'marginRight' => 900,               //отступ справа
            'marginTop' => 900,                //отступ сверху
            'marginBottom' => 900,
            );          //отступ снизу
        $this->section = $this->objectDoc->addSection($sectionStyle);
        $this->addHeadText($AJusticeOfThePeace,true);
//        $this->addHeadText('',true);
        $this->addHeadText('Заявитель:',true);
        $this->addHeadText($this->H['name_organization']);
        $this->addHeadText('Адрес:');
        $this->addHeadText($this->H['address']);
        $this->addHeadText('',false,false);
        $this->addPeopleInToHeader();
        // перечень людей для суда
        $this->addHeadText("Сумма задолженности: ".$this->H['accrual']."р.",true);
        if ($this->H['PenaltyOff'] == 1){
            $this->addHeadText("Сумма пени " . $this->H['SumPenalty'] . " р.",true);
        }
        $this->addHeadText("Госпошлина: ".$this->H['GosPoshlina']."р.",true);

        $this->addCenterText("");
        $this->addCenterText("Заявление о выдачи судебного приказа");
        $this->addCenterText('о взыскании задолженности за жилищные услуги');
        $this->addCenterText("");

        $this->addText("В соответствии с правилами содержания общего имущества в многоквартирном доме, утвержденными Постановлением Правительства ".
            "РФ № 491 от 13.08.2006г., {$this->H['name_organization']} оказывает услуги по обслуживанию и содержанию общего имущества многоквартирного дома и ".
            "выполняет работы по текущему ремонту внутридомовых инженерных сетей, наружных вводов и выпусков, конструктивных элементов здания, работы ".
            "по обеспечению содержания жилого помещения и его оборудования  в надлежащем техническом состоянии, производит уборку подъездов, вестибюлей, ".
            "тамбуров, лестничных клеток и придомовой территории.");
        $this->addText("Согласно справке паспортного стола от XX.XX.XXXX года по адресу: ".$this->H['addressHouse']." зарегистрированы: {$this->H['FIOSumm']}",false,true);

        //$this->addPeopleInToBody();

        $this->addText("Собственники и наниматели помещений обязаны нести бремя расходов на содержание общего имущества соразмерно ".
            "своим долям в праве общей собственности на это имущество путем внесения платы за содержание и ремонт жилого помещения в многоквартирном доме.".
            " Плата за содержание и ремонт жилого помещения устанавливается в размере, обеспечивающем содержание общего имущества в соответствии".
            " с требованиями законодательства Российской Федерации, включая оплату расходов на содержание и ремонт внутридомовых инженерных сетей ".
            "электро-, тепло-, газо- и водоснабжения, водоотведения включая истребование задолженности с собственников помещений, не выполняющих ".
            "надлежащим образом свои обязательства по оплате жилых помещений и коммунальных услуг",false,true);

        $this->addText("В силу ст. ст. 153, 154, 155 ЖК РФ граждане и организации обязаны своевременно и полностью вносить плату за жилое помещение ".
            "и коммунальные услуги. Плата за жилое помещение и коммунальные услуги носиться ежемесячно до десятого числа месяца, следующего за истекшим ".
            "месяцем, если иной срок не установлен договором управления многоквартирным домом.",false,true);

        $this->addText("Однако, должником были нарушены ст.ст. 153, 154, 155 Жилищного кодекса РФ., п.28 Правил и условия договора в ".
            "части своевременного внесения платы за оказанные им услуги. ",false,true);


        $textRun = $this->section->createTextRun($this->style2);
        $textRun->addText("В результате, по данным {$this->H['name_organization']}, за должником по состоянию на ",$this->SF_Norm);
        $textRun->addText("{$this->H['dateEnd']} года числится",$this->SF_Norm);
        $textRun->addText(" задолженность",$this->SF_bold);
        $textRun->addText(" за оказанные услуги и выполненные работы",$this->SF_Norm);
        $textRun->addText(" в размере {$this->H['accrual']} руб.",$this->SF_bold);


        $this->addText("Период образования задолженности: {$this->H['dateStart']} года по {$this->H['dateEnd']} года",true,true);
        $this->addText("В соответствии с п.14. ст. 155 ЖК РФ лица, несвоевременно и (или) не полностью внесшие плату за жилое помещение и коммунальные ".
            "услуги, обязаны уплатить кредитору пени в размере одной трехсотой ставки рефинансирования Центрального банка Российской Федерации, ".
            "действующей на день фактической оплаты, от не выплаченной в срок суммы за каждый день просрочки начиная с тридцать первого дня, ".
            "следующего за днем наступления установленного срока оплаты, по день фактической оплаты, произведенной в течение девяноста календарных ".
            "дней со дня наступления установленного срока оплаты, либо до истечения девяноста календарных дней после дня наступления установленного срока оплаты, ".
            "если в девяностодневный срок оплата не произведена. Начиная с девяносто первого дня, следующего за днем наступления установленного срока оплаты, ".
            "по день фактической оплаты пени уплачиваются в размере одной стотридцатой ставки рефинансирования Центрального банка Российской Федерации, ".
            "действующей на день фактической оплаты, от не выплаченной в срок суммы за каждый день просрочки. Увеличение установленных настоящей частью ".
            "размеров пеней не допускается.",false,true);
        $this->addText("Сумма пени за период с {$this->H['dateStartPenalty']} по {$this->H['dateEnd']} гг составляет  {$this->H['SumPenalty']} руб.",true,true);
        $this->addText("На основании вышеизложенного и руководствуясь ст.ст. 153, 154, 155 ЖК РФ, ст.ст. 779, 782, 1102, 1105 ГК РФ, ст.ст. 122-124 ГПК РФ.",false,true);

        $this->addText("ПРОШУ:",true,true);
        $solidarity = "солидарное";
        if ($this->countOfHuman == 1) // если один человек в приказе то не используем слово "солиданое" в сочитании "солиданое взыскание"
            $solidarity = "";
        $this->addText("Выдать судебный приказ на $solidarity взыскание в пользу {$this->H['name_organization_full']}:",false,true);


        $this->addText("1.	Задолженность по оплате за обслуживание жилья и текущий ремонт мест общего пользования в размере:{$this->H['accrual']} р.  за период c {$this->H['dateStart']} по {$this->H['dateEnd']} гг" .
            ", пени в размере {$this->H['SumPenalty']} за период c {$this->H['dateStart']} по {$this->H['dateEnd']} гг",false,true);
        $this->addText("2.	Оплаченной госпошлины в размере {$this->H['GosPoshlina']} руб.",false,true);
        //$this->addText("",true,true);
        $this->addText("ВСЕГО: {$this->H['summa']} ({$this->H['text']})",true,true);



        $this->addText("Приложение:",false,true);
        $this->addText("1.	Справка о начислении за ЖКУ на {$this->H['dateEnd']}",false,true);
        $this->addText("2.	Справка паспортного стола от {$this->H['dateEnd']}",false,true);
        $this->addText("3.	Квитанция об оплате госпошлины.",false,true);
        $this->addText("4.	Копия почтовой квитанции о вручении заявления о выдачи судебного приказа.",false,true);
        $this->addText("5.	Копия доверенности на представителя ",false,true);
        if ($this->H['PenaltyOff'] == 1){
            $this->addText("6.	Расчет пени.",false,true);
        }

/*        $this->addText("3.	Копия договора управления МКД № XXXXX от XX.XX.XX.",false,true);
        $this->addText("4.	Копия свидетельства о государственной регистрации",false,true);
        $this->addText("5.	Копия свидетельства о постановке на налоговый учет ",false,true);
        $this->addText("6.	Копия Устава.",false,true);
*/

        $this->addText("");
        $this->addText("Представитель ",false,true);
        $this->addText("{$this->H['name_organization']}                               __________________   /$Signatory/",false,true);
        $this->addText("(по доверенности) ",false,true);
        $this->addText("");

    }


    private function addPeopleInToHeader()
    {
        $this->countOfHuman = 0;
        foreach ($this->data as  $key => $t1){
            $textRun = $this->section->createTextRun($this->style3);
            $textRun->addText("Должник: ",$this->SF_bold);
            $textRun->addText("{$t1['FIO']} {$t1['birthday']} г.р.",$this->SF_Norm);

            $textRun = $this->section->createTextRun($this->style3);
            $textRun->addText("Уроженец (ка): ",$this->SF_bold);
            $textRun->addText("{$t1['caption']}",$this->SF_Norm);

            $textRun = $this->section->createTextRun($this->style3);
            $textRun->addText("Паспорт: ",$this->SF_bold);
            $textRun->addText("{$t1['s_doc']} {$t1['n_doc']} {$t1['data_create']} {$t1['issued_by']}",$this->SF_Norm);

            $textRun = $this->section->createTextRun($this->style3);
            $textRun->addText("Проживающий:",$this->SF_bold);
            $textRun->addText($this->H['addressHouse'],$this->SF_Norm);

            $this->addHeadText('',false,false);
            $this->countOfHuman ++;
        }
    }

    private function addPeopleInToBody()
    {
        foreach ($this->data as  $key => $t2){
            $this->addText("".$t2['FIO']." ".$t2['birthday']. " г.р. ".$t2['native']." ".$t2['caption'],true,true);
        }
    }
    private function addHeadText($text,$bold = false)
    {
        $this->section->addText(
            $text,
            array('bold' => $bold,
                'spaceBefore' => 0,
                'spaceAfter' => 0),
            array('indentation' => array('left' => self::Left, 'right' => 0),
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
        }else{
            $bold = array('bold' => true);
        }
        $this->section->addText(
            $text,
            $bold,
            $style2
        );

    }
}