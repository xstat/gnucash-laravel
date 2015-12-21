<?php

namespace App\Json;

use App;

class Transaction
{
    public static function toArray($transaction)
    {
        $array = $transaction->getData();

        $array['amount'] = App::make('CurrencyHelper')->formatArray(
            $transaction->getAmount()
        );

        return $array;
    }

    public static function toJson($transaction)
    {
        return json_encode(self::toArray($transaction));
    }
}