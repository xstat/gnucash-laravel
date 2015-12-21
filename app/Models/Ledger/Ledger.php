<?php

namespace App\Models\Ledger;

use App;

class Ledger
{
    protected $repo;
    protected $calculator;
    protected $periods;

    public function __construct($repository)
    {
        $this->repo = $repository;

        $this->calculator = App::make('TimeCalculator',
            (array) $this->repo->getFullInterval()
        );
    }

    public function calculator()
    {
        return $this->calculator;
    }

    public function getPeriods()
    {
        if ($this->periods === null) {
            $this->load();
        }

        return $this->periods;
    }

    public function from($date)
    {
        $this->calculator()->setDateStart($date);
        return $this;
    }

    public function to($date)
    {
        $this->calculator()->setDateEnd($date);
        return $this;
    }

    public function every($length, $step)
    {
        $this->calculator()->setLength($length);
        $this->calculator()->setStep($step);
        return $this;
    }

    public function load()
    {
        $this->periods = [];

        foreach ($this->calculator()->getIntervals() as $interval) {
            $this->periods[] = App::make('LedgerPeriod', [$interval]);
        }

        return $this;
    }

    public function loadTransactions()
    {
        foreach ($this->getPeriods() as $period) {
            $period->loadTransactions();
        }

        return $this;
    }

    public function stats()
    {
        return $this;
    }

    public function get()
    {
        return $this->periods;
    }

    public function setIntervalCode($intervalCode)
    {
        $this->calculator()->setIntervalCode($intervalCode);
        return $this;
    }
}