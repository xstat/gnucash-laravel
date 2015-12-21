<?php

namespace App\Models\Ledger;

use App;
use App\Json\Period as JsonPeriod;
use App\Helpers\Date as DateHelper;

class Period
{
    protected $interval;

    protected $accounts = [];
    protected $transactions = [];
    protected $totals = [];

    protected $filter = [];
    protected $root = [];

    // A custom account to use as the root account.
    protected $customRoot;


    public function __construct($interval)
    {
        $this->interval = $interval;
    }

    public function loadAccounts()
    {
        $this->accounts = App::make('GncRepo')->getAccountsWithTotals(
            $this->interval, $this->getFilter('account_type')
        );

        $this->addTransactionsToAccounts();

        return $this;
    }

    public function loadTransactions()
    {
        $this->transactions = App::make('GncRepo')->getTransactions(
            $this->interval, $this->getFilter('account_type')
        );

        $this->addTransactionsToAccounts();

        return $this;
    }

    public function loadTotals()
    {
        $this->totals = App::make('GncRepo')
            ->getTotalsByAccountType(null, $this->interval);

        $profit = $this->totals['INCOME'] + $this->totals['EXPENSE'];
        $this->totals['PROFIT'] = $profit;

        return $this;
    }

    public function loadAncestors($accounts = null)
    {
        $accounts = $accounts ? : $this->accounts;

        foreach ($accounts as $account) {

            if ($parentAccount = $this->getParentAccount($account)) {

                $parentAccount->addAccount($account);

                if ($this->isSubTreeRoot($parentAccount)) {
                    if (!in_array($parentAccount, $this->root)) {
                        $this->root[] = $parentAccount;
                    }
                } else {
                    $this->loadAncestors([$parentAccount]);
                }
            }
        }

        return $this;
    }

    protected function addTransactionsToAccounts()
    {
        foreach ($this->transactions as $transaction) {
            if ($account = $this->getAccount($transaction->getAccountId())) {
                $account->addTransaction($transaction);
            }
        }
    }

    protected function isSubTreeRoot($account)
    {
        if ($account->getType() === 'ROOT') {
            return true;
        }

        return $account->getId() === $this->customRoot;
    }

    public function setRootAccount($accountId)
    {
        $this->customRoot = $accountId;
    }

    public function setFilter($name, $value)
    {
        $this->filter[$name] = $value;
        return $this;
    }

    public function getFilter($name)
    {
        return @$this->filter[$name];
    }

    public function getData()
    {
        return $this->interval->toArray();
    }

    public function getAccount($accountId)
    {
        return @$this->accounts[$accountId];
    }

    public function getParentAccount($account)
    {
        $parentId = $account->getParentId();

        if (!$parentAccount = $this->getAccount($parentId)) {
            $parentAccount = App::make('GncRepo')->getAccountById($parentId);
            $this->accounts[$parentId] = $parentAccount;
        }

        return $parentAccount;
    }

    public function getAccounts()
    {
        return $this->accounts;
    }

    public function getRootAccounts()
    {
        return $this->root;
    }

    public function getTransactions()
    {
        return $this->transactions;
    }

    public function getTotals()
    {
        return $this->totals;
    }

    public function toArray()
    {
        return JsonPeriod::toArray($this);
    }

    public function toJson()
    {
        return JsonPeriod::toJson($this);
    }
}