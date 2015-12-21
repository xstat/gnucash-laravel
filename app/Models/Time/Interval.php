<?php

namespace App\Models\Time;

use App\Helpers\Date as DateHelper;

class Interval
{
    const STATUS_PAST    = 'past';
    const STATUS_CURRENT = 'current';
    const STATUS_FUTURE  = 'future';

    protected $_dateStart;
    protected $_dateEnd;
    protected $_status;
    protected $_daysElapsed;
    protected $_daysLeft;


    public function __construct($dateStart = null, $dateEnd = null)
    {
        $this->_dateStart = DateHelper::createDate($dateStart);
        $this->_dateEnd   = DateHelper::createDate($dateEnd);

        $this->_initStatus();
    }

    public function toArray()
    {
        return [
            'code'  => DateHelper::encode($this),
            'start' => DateHelper::formatForSql($this->_dateStart),
            'end'   => DateHelper::formatForSql($this->_dateEnd)
        ];
    }

    protected function _initStatus()
    {
        $today = DateHelper::now();

        if ($today >= $this->_dateStart && $today <= $this->_dateEnd) {

            $this->_status      = self::STATUS_CURRENT;
            $this->_daysElapsed = DateHelper::now()->format('d');
            $this->_daysLeft    = $this->getDaysCount() - $this->_daysElapsed;

        } else if ($today < $this->_dateStart) {

            $this->_status      = self::STATUS_FUTURE;
            $this->_daysElapsed = 0;
            $this->_daysLeft    = $this->getDaysCount();

        } else {

            $this->_status      = self::STATUS_PAST;
            $this->_daysElapsed = $this->_dateEnd->format('d');
            $this->_daysLeft    = 0;
        }
    }

    public function isFuture()
    {
        return $this->_status == self::STATUS_FUTURE;
    }

    public function isPresent()
    {
        return $this->_status == self::STATUS_CURRENT;
    }

    public function isPast()
    {
        return $this->_status == self::STATUS_PAST;
    }

    public function getYear()
    {
        return (int) $this->_dateStart->format('Y');
    }

    public function getMonth()
    {
        return (int) $this->_dateStart->format('n');
    }

    public function getDaysElapsed()
    {
        return $this->_daysElapsed;
    }

    public function getDaysLeft()
    {
        return $this->_daysLeft;
    }

    public function getDaysCount()
    {
        return (int) $this->_dateEnd->format('d');
    }

    public function getStartDate()
    {
        return $this->_dateStart;
    }

    public function getEndDate()
    {
        return $this->_dateEnd;
    }
}