<?php

namespace App\Json;

use App;

class Account
{
    public static function toArray($account)
    {
        $array = $account->getData();

        foreach ($account->getAccounts() as $childAccount) {
            $array['accounts'][] = $childAccount->toArray();
        }

        foreach ($account->getTransactions() as $transaction) {
            $array['transactions'][] = $transaction->toArray();
        }

        $array['total'] = App::make('CurrencyHelper')
            ->formatArray($array['total']);

        return $array;
    }

    public static function toJson($account)
    {
        return json_encode(self::toArray($account));
    }
}