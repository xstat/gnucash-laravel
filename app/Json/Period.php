<?php

namespace App\Json;

use App;

class Period
{
    public static function toArray($period)
    {
        $array = $period->getData();

        foreach ($period->getRootAccounts() as $account) {
            $array['accounts'][] = $account->toArray();
        }

        // foreach ($period->getTransactions() as $transaction) {
        //     $array['transactions'][] = $transaction->toArray();
        // }

        foreach ($period->getTotals() as $type => $total) {
            $array['totals'][$type] = App::make('CurrencyHelper')
                ->formatArray($total);
        }

        // $array['root'] = $period->getRootAccounts();

        return $array;
    }

    public static function toJson($period)
    {
        return json_encode(self::toArray($period));
    }
}