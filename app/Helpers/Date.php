<?php

namespace App\Helpers;

use App;
use DateTime;

class Date
{
    public static function now()
    {
        return new DateTime();
    }

    public static function createDate($mixed)
    {
        if ($mixed instanceof DateTime) {
            return $mixed;
        }
        return new DateTime($mixed);
    }

    public static function encode($interval)
    {
        return base64_encode(json_encode([
            'from' => static::formatForSql($interval->getStartDate()),
            'to' => static::formatForSql($interval->getEndDate())
        ]));
    }

    public static function decode($intervalCode)
    {
        return json_decode(base64_decode($intervalCode));
    }

    public static function beginOfTheDay($date)
    {
        return $date->modify('midnight');
    }

    public static function endOfTheDay($date)
    {
        return $date->modify('midnight + 1 day - 1 second');
    }

    public static function formatForSql(DateTime $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public static function formatForHuman(DateTime $date)
    {
        return $date->format('Y M j H:i');
    }
}