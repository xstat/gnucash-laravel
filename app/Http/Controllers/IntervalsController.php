<?php

namespace App\Http\Controllers;

use App;
use Request;
use App\Http\Controllers\Controller;

class IntervalsController extends Controller
{
    public function getPeriods()
    {
        $ledger = App::make('Ledger')->load();

        $response = [];

        foreach ($ledger->getPeriods() as $period) {
            $response[] = $period->loadTotals()->toArray();
        }

        return response()->json($response);
    }

    public function getPeriodDetailByType()
    {
        $interval = Request::input('interval');
        $accountType = Request::input('account_type');

        $ledger = App::make('Ledger')
            ->setIntervalCode($interval)
            ->load();

        $period = current($ledger->getPeriods())
            ->setFilter('account_type', $accountType)
            ->loadAccounts()
            ->loadAncestors()
            ->loadTransactions();

        return response()->json($period->toArray());
    }
}