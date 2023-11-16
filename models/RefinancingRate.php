<?php


namespace models;


use DB\Connect;
//use \ProdCalendar\Calendar;

class RefinancingRate
{
    private $dateEnd;

    
    public function setDateEnd(string $dateEnd): void
    {
        $this->dateEnd = new \DateTimeImmutable($dateEnd);
        //$this->dateEnd = $this->dateEnd->modify('+1 day');
    }

    public function update()
    {
        $dateStart = $this->getOldDateIntoDB();
        $deltaDay = (int) ($dateStart->diff($this->dateEnd)->format('%R%a') * 1);

        if ($deltaDay <= 1) // если разница в днях 2 или меньше, значит в базе есть все ставки рефинансирования
            return true; //по идее должны вывалиться без обработки

        if ( ($deltaDay > 1) && ($deltaDay < 10) ){
            $this->deleteLastTenDays(); //Удалим последние 10 записей в базе (чтобы небыло косяка при запросе в СБРФ)
            $dateStart = $this->getOldDateIntoDB();
        }


        $cbrf = new \Liquetsoft\CbrfService\CbrfDaily();
        try {
            $keyRate = $cbrf->keyRate(
                $dateStart,
                $this->dateEnd
            );
        }catch (\Exception $e){
            \models\ErrorLog::saveError('-------------------','ErrRefinancingRate.err');
            \models\ErrorLog::saveError($dateStart,'ErrRefinancingRate.err');
            \models\ErrorLog::saveError($this->dateEnd,'ErrRefinancingRate.err');
            \models\ErrorLog::saveError('===================','ErrRefinancingRate.err');
            \models\ErrorLog::saveError($e,'ErrRefinancingRate.err');
        }


        $calendar = new \Devmakis\ProdCalendar\Calendar();

        $keyRate_array = Array();
        foreach ($keyRate as $key => $object){
            $date = $object->getDate();
            $keyRate_array[$date->format("d.m.Y")] = $object->getRate();

        }
        \models\ErrorLog::saveError($keyRate);
        \models\ErrorLog::saveError($keyRate_array);

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
        $oldDate_string = $conn->complexQuery("SELECT MAX(id_date) AS oldDate FROM refinancingRate")->fetchField('oldDate');
        $oldDate = new \DateTimeImmutable("$oldDate_string");

        return  $oldDate;
    }

    private function deleteLastTenDays()
    {
        $conn = new \DB\Connect(\DB\Connect::GD);
        $query="
        delete refinancingRate
            from (
                SELECT        TOP (10) year, month, day
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