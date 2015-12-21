<?php

namespace App\Models\Time;

use App;
use App\Models\Time\Interval as TimeInterval;
use App\Helpers\Date as DateHelper;

class Calculator
{
    const STEP_DAY   = 'days';
    const STEP_WEEK  = 'weeks';
    const STEP_MONTH = 'months';
    const STEP_YEAR  = 'years';


    private $dateStart;
    private $dateEnd;
    private $step;
    private $length;
    private $interval;

    public function __construct($dateStart, $dateEnd)
    {
        $this->length    = 1;
        $this->step      = self::STEP_MONTH;
        $this->dateStart = DateHelper::createDate($dateStart);
        $this->dateEnd   = DateHelper::createDate($dateEnd);
    }

    public function setDateStart($dateStart)
    {
        $this->dateStart = DateHelper::createDate($dateStart);
    }

    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = DateHelper::createDate($dateEnd);
    }

    public function setStep($step)
    {
        $this->step = $step ? : $this->step;

        return $this;
    }

    public function setLength($length)
    {
        if (($length = intval($length)) > 0) {
            $this->length = $length;
        }

        return $this;
    }

    public function getFullInterval()
    {
        return $this->createInterval(
            $this->dateStart,
            $this->dateEnd
        );
    }

    public function getIntervals()
    {
        if ($this->interval) {

            // An interval has been set by code, there
            // is no need to calculate anything.
            return [$this->interval];

        } else {
            return $this->calculateIntervals();
        }
    }

    private function calculateIntervals()
    {
        $intervals = array();

        $current = clone $this->dateStart;

        while ($current <= $this->dateEnd) {

            $start = clone $current;
            $this->applyStep($current);
            $end   = clone $current;

            $intervals[] = $this->createInterval(
                $start, $end
            );

            $current->modify('+1 days');
        }

        return $intervals;
    }

    private function createInterval($dateStart, $dateEnd)
    {
        return App::make('TimeInterval', [
            DateHelper::beginOfTheDay($dateStart),
            DateHelper::endOfTheDay($dateEnd)
        ]);
    }

    private function applyStep(&$date)
    {
        switch ($this->step) {

            case self::STEP_WEEK :
                $date->modify('this week - 1 day + 6 days');
                break;

            case self::STEP_MONTH :
                $date->modify('last day of this month');
                break;

            case self::STEP_YEAR :
                $date->modify('1/1 next year - 1 day');
                break;
        }

        if ($length = $this->length - 1) {
            $date->modify(sprintf('+%d %s', $length, $this->step));
        }

        if ($date > $this->dateEnd) {
            $date = clone $this->dateEnd;
        }
    }

    public function setIntervalCode($intervalCode)
    {
        if ($interval = DateHelper::decode($intervalCode)) {
            $this->interval = $this->createInterval(
                DateHelper::createDate($interval->from),
                DateHelper::createDate($interval->to)
            );
        }
    }
}