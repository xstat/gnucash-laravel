<?php

namespace App\Models\Ledger;

use App\Helpers\Date as DateHelper;
use App\Json\Transaction as JsonTransaction;

class Transaction
{
    protected $data;

    public function __construct($data)
    {
        $this->data = (array) $data;

        $this->data['post_date'] = DateHelper::createDate(
            $this->data['post_date']
        );
    }

    public function getValue($attribute)
    {
        if (isset($this->data[$attribute])) {
            return $this->data[$attribute];
        }

        return null;
    }

    public function getAccountId()
    {
        return $this->getValue('account_guid');
    }

    public function getDescription()
    {
        return $this->getValue('description');
    }

    public function getData()
    {
        return $this->data;
    }

    public function getAmount()
    {
        return $this->getValue('amount');
    }

    public function toArray()
    {
        return JsonTransaction::toArray($this);
    }

    public function toJson()
    {
        return JsonTransaction::toJson($this);
    }
}