<?php


namespace models;


use DB\Connect;
//use \ProdCalendar\Calendar;

class RefinancingRate
{
    private $dateEnd;

    private $dateStart = false;

    /**
     * @param mixed $dateSart
     */
    public function setDateStart($dateStart): void
    {
        $this->dateStart = $dateStart;
    }

    public function setDateEnd(string $dateEnd): void
    {
        $this->dateEnd = new \DateTimeImmutable($dateEnd);
        //$this->dateEnd = $this->dateEnd->modify('+1 day');
    }

    public function update()
    {
        \models\ErrorLog::saveError('start','LogRefinancingRate.err','w+');
        $this->deleteLastTenDays(); //Удалим последние 10 записей в базе (чтобы небыло косяка при запросе в СБРФ)
        $dateStart = $this->getOldDateIntoDB();
        \models\ErrorLog::saveError($dateStart->format('d.m.Y'),'LogRefinancingRate.err');
        \models\ErrorLog::saveError('-------------------','ErrRefinancingRate.err');
        \models\ErrorLog::saveError($dateStart,'ErrRefinancingRate.err');
        \models\ErrorLog::saveError($this->dateEnd,'ErrRefinancingRate.err');
        $cbrf = new \Liquetsoft\CbrfService\CbrfDaily();
        try {
            $keyRate = $cbrf->keyRate(
                $dateStart,
                $this->dateEnd
            );
        }catch (\Exception $e){
            \models\ErrorLog::saveError('Error','ErrRefinancingRate.err');
            \models\ErrorLog::saveError($e,'ErrRefinancingRate.err');
        }


        $calendar = new \Devmakis\ProdCalendar\Calendar();

        $keyRate_array = Array();
        foreach ($keyRate as $key => $object){
            $date = $object->getDate();
            $keyRate_array[$date->format("d.m.Y")] = $object->getRate();

        }
        \models\ErrorLog::saveError($keyRate,'LogRefinancingRate.err');
        \models\ErrorLog::saveError($keyRate_array,'LogRefinancingRate.err');

        $dateStart = $dateStart->modify('+1 day');

        $conn = new Connect(Connect::GD);
        //$conn->table('refinancingRate')->delete();

        while ((int) $this->dateEnd->diff($dateStart)->format("%a") <> 0){

            $txtDate = $dateStart->format("d.m.Y");
            if (array_key_exists($txtDate,$keyRate_array)){
                $rate = $keyRate_array[$txtDate];
            }
            $year = $dateStart->format("Y");
            $month = $dateStart->format("m");
            $day = $dateStart->format("d");
            $id_date = $dateStart->format("d.m.Y");
            $nextDate = $calendar->nextJobDay($dateStart)->format("d.m.Y");
            $weekend = $calendar->isNonWorking($dateStart) === true ? '1' : "0";

            try{
                $conn->table('refinancingRate')
                    ->set('year',$year)
                    ->set('month',$month)
                    ->set('day',$day)
                    ->set('id_date',$id_date)
                    ->set('nextDate',$nextDate)
                    ->set('rate',$rate)
                    ->set('weekend',$weekend)
                    ->insert();
            }catch (\PDOException $e){}
            \models\ErrorLog::saveError("END.",'LogRefinancingRate.err');

            $dateStart = $dateStart->modify('+1 day');
        }

        return true;
    }

    /**
     * @return \DateTimeImmutable
     */
    private function getOldDateIntoDB()
    {
        $conn = new \DB\Connect(\DB\Connect::GD);

        $query = "
        SELECT        ISNULL(MIN(id_date),
                             (
							 SELECT        ISNULL(MAX(id_date), '01.01.2020') AS oldDate
                               FROM            refinancingRate
							   )
					) AS oldDate
                FROM            refinancingRate AS refinancingRate_1
                WHERE        (rate IS NULL)
        ";
        $oldDate_string = $conn->complexQuery($query)->fetchField('oldDate');
        /*
         *  по умочанию дата начала поиска ставки рефинансирования должна вычисляться
         * но мы можем задать этот диапазон в ручную
         */
        \models\ErrorLog::saveError("вычисленная дада начала поиска ставки рефинансирования - $oldDate_string",'LogRefinancingRate.err');

        if ($this->dateStart !== false){

            $oldDate_string = $this->dateStart;
        }
        \models\ErrorLog::saveError("итоговая дада начала поиска ставки рефинансирования - $oldDate_string",'LogRefinancingRate.err');

        $oldDate = new \DateTimeImmutable("$oldDate_string");

        return  $oldDate;
    }

    private function deleteLastTenDays()
    {
        $this->deleteLastXDays(10);
        while ($this->deleteWeekend()) {
            $this->deleteLastXDays(1);
        }
        $this->deleteLastXDays(1);

    }

    private function deleteWeekend()
    {
        $conn = new \DB\Connect(\DB\Connect::GD);
        $query="SELECT        TOP (1) weekend FROM refinancingRate ORDER BY id_date DESC";
        $weekend = $conn->complexQuery($query)->fetchField('weekend');
        print chr(10).chr(13);
        print "===".$weekend.chr(10).chr(13);
        if ($weekend == '1')
            return true;
        else
            return false;

    }
    private function deleteLastXDays($x)
    {
        $conn = new \DB\Connect(\DB\Connect::GD);
        $query="
        delete refinancingRate
            from (
                SELECT        TOP ($x) year, month, day
                FROM            refinancingRate
                ORDER BY id_date DESC
            ) as t
        where 
            refinancingRate.year = t.year AND
            refinancingRate.month = t.month AND
            refinancingRate.day = t.day
        ";
        $conn->complexQuery($query);

    }
}