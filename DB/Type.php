<?php


namespace DB;
/**
 * Class Type
 * @package DB
 * типы полей использумых в создании таблиц
 */

class Type
{
    const bit = 'bit';

    const smallint = 'smallint';

    const int = 'int';

    const bigint = 'bigint';

    const money = 'money';

    const nvarchar  = 'nvarchar';

    const ntext = 'ntext';

    const text = 'text';

    const varchar  = 'varchar';

    const date = 'date';

    const datetime = 'datetime';

    const smalldatetime = 'smalldatetime';

    const image = 'image';

    const tinyint = 'tinyint';

    const uniqueidentifier = 'uniqueidentifier';

    const numeric = 'numeric';
    /**
     * Возвращает false для типов без определения размера, либо указанный размер
     * @param $type
     * @param $size
     * @return false|mixed
     */
    static function getSizeFalseForType($type,$size)
    {
        switch ($type){
            case self::bit:
            case self::smallint:
            case self::int:
            case self::bigint:
            case self::tinyint:
            case self::money:
            case self::date:
            case self::datetime:
            case self::smalldatetime:
            case self::image:
            case self::uniqueidentifier:
                return false;

            default :
                return $size;
        }

    }
}

