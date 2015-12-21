<?php

namespace App\Helpers;

class Currency
{
    public function fixSign($number)
    {
        return $number * -1;
    }

    public function format($amount)
    {
        return sprintf('$ %10s', number_format($amount, 2));
    }

    public function formatArray($amount)
    {
        return [
            'total' => $amount,
            'formatted' => $this->format($amount)
        ];
    }
}