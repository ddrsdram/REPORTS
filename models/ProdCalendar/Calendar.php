<?php
/*
 <!--
year    - год на который сформирован календарь
lang    - двухбуквенный код языка на котором представлены названия праздников
date    - дата формирования xml-календаря в формате ГГГГ.ММ.ДД
country - двухбуквенный код страны
-->
<calendar year="2014" lang="ru" date="2014.01.01" country="ru">
	<!--
		holidays - Список праздников
		id - идентификатор праздника
		title - название праздника
	-->
	<holidays>
		<holiday id="1" title="Новогодние каникулы" />
		<holiday id="2" title="Рождество Христово" />
		<holiday id="3" title="День защитника Отечества" />
		<holiday id="4" title="Международный женский день" />
		<holiday id="5" title="Праздник Весны и Труда" />
		<holiday id="6" title="День Победы" />
		<holiday id="7" title="День России" />
		<holiday id="8" title="День народного единства" />
	</holidays>
	<!--
		days - праздники/короткие дни/рабочие дни (суббота либо воскресенье)
		d (day) - день (формат ММ.ДД)
		t (type) - тип дня: 1 - выходной день, 2 - рабочий и сокращенный (может быть использован для любого дня недели), 3 - рабочий день (суббота/воскресенье)
		h (holiday) - номер праздника (ссылка на атрибут id тэга holiday)
		f (from) - дата с которой был перенесен выходной день
		суббота и воскресенье считаются выходными, если нет тегов day с атрибутом t=2 и t=3 за этот день
	-->
	<days>
		<day d="01.01" t="1" h="1" />
		<day d="01.02" t="1" h="1" />
		<day d="01.03" t="1" h="1" />
		<day d="02.22" t="1" f="01.03" />
		...
	</days>
</calendar>
 */

namespace Devmakis\ProdCalendar;

use \DateTimeImmutable;
use \SimpleXMLElement;
use \DateInterval;
class Calendar
{
    private  $publicHoliday = Array();
    private $xml;
    private $year;
    public function __construct()
    {

    }


    /**
     * @param DateTimeImmutable $date
     * @return DateTimeImmutable
     */
    public function nextJobDay(DateTimeImmutable $date): DateTimeImmutable
    {
        //$timeZone = $date->getTimezone();

        if ($this->isNonWorking ($date)){
            $next_day = $date->modify('+1 day');
            return $this->nextJobDay($next_day);
        }else{
            return $date;
        }

    }


    /**
     * @param DateTimeImmutable $date
     * @return false
     */
    public function isNonWorking( DateTimeImmutable $date): bool
    {
        return $this->isPublicHoliday($date) || $this->isWeekend($date);
    }


    /**
     * @param DateTimeImmutable $date
     * @return bool
     */
    public function isWeekend( DateTimeImmutable $date): bool
    {
        $weekend = $date->format('N');
        if (($weekend == 6) || ($weekend == 7))
            return true;
        else
            return false;
    }


    /**
     * @param DateTimeImmutable $date
     * @return bool
     */
    public function isPublicHoliday(DateTimeImmutable $date): bool
    {
        $this->year = $date->format('Y');
        $monthDay = $date->format('m.d');

        if (!array_key_exists($this->year,$this->publicHoliday)){
            $this->loadPublicHolidayFromFile("$this->year.xml");
        }

        $yearOfSearch = $this->publicHoliday[$this->year];

        if (array_search($monthDay,$yearOfSearch) !== false){
            return true;
        }else{
            return false;
        }

    }

    /**
     * @param $file
     */
    private function loadPublicHolidayFromFile($file)
    {
        $this->LoadXml($file);
        $this->XmlToArray();
    }

    /**
     * @param $file
     */
    private function LoadXml($file)
    {
        $file = __DIR__."/XML/$file";
        $fh = fopen($file,"r");
        $this->xml = fread($fh,filesize($file));
        fclose($fh);
    }

    /**
     * @throws \Exception
     */
    private function XmlToArray()
    {
        $fabric = new SimpleXMLElement ($this->xml);
        $addArray = Array();
        foreach ($fabric->days->day as $key => $item){
            if ((int) $item['t'] == 1)
                $addArray[] = (string) $item['d'];
        }
        $this->publicHoliday[$this->year] = $addArray;

    }
}