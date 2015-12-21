<?php

namespace App\Models\Ledger;

use App\Json\Account as JsonAccount;

class Account
{
    protected $data;
    protected $accounts = [];
    protected $transactions = [];

    public function __construct($data)
    {
        $fields = [
            'total' => 0
        ];

        $this->data = array_replace($fields, (array) $data);
    }

    public function getValue($attribute, $defaultValue = null)
    {
        if (isset($this->data[$attribute])) {
            return $this->data[$attribute];
        }

        return $defaultValue;
    }

    public function getId()
    {
        return $this->getValue('guid');
    }

    public function getType()
    {
        return $this->getValue('account_type');
    }

    public function getData()
    {
        return $this->data;
    }

    public function getParentId()
    {
        return $this->getValue('parent_guid');
    }

    public function addAccount($account)
    {
        $this->accounts[$account->getId()] = $account;
    }

    public function getAccounts()
    {
        return $this->accounts;
    }

    public function addTransaction($transaction)
    {
        $this->transactions[] = $transaction;
    }

    public function getTransactions()
    {
        return $this->transactions;
    }

    public function toArray()
    {
        return JsonAccount::toArray($this);
    }

    public function toJson()
    {
        return JsonAccount::toJson($this);
    }

    public function __toString()
    {
        return sprintf('(%s) %s',
            $this->data->account_type,
            $this->data->name
        );
    }
}