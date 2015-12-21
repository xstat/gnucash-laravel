<?php

namespace App\Repositories;

use DB;
use App;
use DateTime;
use App\Helpers\Date as DateHelper;
use App\Models\Time\Interval as TimeInterval;

class GnuCashRepository
{
    const ACCOUNT_TYPE_EXPENSE = 'EXPENSE';
    const ACCOUNT_TYPE_ASSET   = 'ASSET';
    const ACCOUNT_TYPE_INCOME  = 'INCOME';
    const ACCOUNT_TYPE_EQUITY  = 'EQUITY';
    const ACCOUNT_TYPE_BANK    = 'BANK';
    const ACCOUNT_TYPE_ROOT    = 'ROOT';

    protected $accounts;

    public function __construct()
    {
        $this->accounts = $this->getAccounts();
    }

    public function getAccounts()
    {
        return $this->createAccountsFromArray(
            DB::table('accounts')->get()
        );
    }

    public function getAccountsWithTotals($interval = null, $accountType = null)
    {
        $query = DB::table('splits')
            ->select('accounts.*', DB::raw('SUM(value_num / value_denom) AS total'))
            ->join('accounts', 'accounts.guid', '=', 'splits.account_guid')
            ->join('transactions', 'transactions.guid', '=', 'splits.tx_guid')
            ->groupBy('account_guid');

        if (is_string($accountType)) {
            $query->where('accounts.account_type', '=', $accountType);
        }

        return $this->createAccountsFromArray(
            $this->applyTimeConstraints($query, $interval)->get()
        );
    }

    public function getAccountsWithTransactions($interval = null, $accountType = null)
    {
        $accounts = $this->getAccountsWithTotals($interval, $accountType);

        foreach ($this->getTransactions($interval, $accountType) as $transaction) {
            $accounts[$transaction->account_guid]->addTransaction($transaction);
        }

        return $accounts;
    }

    public function getTransactions($interval, $accountType = null)
    {
        $query = DB::table('splits')
            ->select('*', DB::raw('value_num / value_denom AS amount'))
            ->join('accounts', 'accounts.guid', '=', 'splits.account_guid')
            ->join('transactions', 'transactions.guid', '=', 'splits.tx_guid');

        if (is_string($accountType)) {
            $query->where('accounts.account_type', '=', $accountType);
        }

        return $this->createTransactionsFromArray(
            $this->applyTimeConstraints($query, $interval)->get()
        );
    }

    public function getTotalsByAccountType($accountType, $interval = null)
    {
        $query = DB::table('splits')
            ->select('account_type', DB::raw('SUM(value_num / value_denom) total'))
            ->join('accounts', 'accounts.guid', '=', 'splits.account_guid')
            ->join('transactions', 'transactions.guid', '=', 'splits.tx_guid')
            ->groupBy('accounts.account_type');

        $this->applyTimeConstraints($query, $interval);

        if (is_string($accountType)) {
            $query->where('accounts.account_type', '=', $accountType);
        }

        return $this->createTotalsFromArray($query->get());
    }

    public function getAccountById($accountId)
    {
        if (isset($this->accounts[$accountId])) {
            return clone $this->accounts[$accountId];
        }

        return null;
    }

    // public function getAccountsByType($accountType)
    // {
    //     return DB::table('accounts')
    //         ->where('account_type', $accountType)
    //         ->get();
    // }

    // public function getAccountsByParent($accountId)
    // {
    //     return DB::table('accounts')
    //         ->where('parent_guid', $accountId)
    //         ->get();
    // }

    // public function getAccountTotal($accountId, $mixed = null)
    // {
    //     return floatval(DB::table('splits')
    //         ->select(DB::raw('IFNULL(SUM(value_num / value_denom), 0) AS total'))
    //         ->leftJoin('transactions', 'transactions.guid', '=', 'splits.tx_guid')
    //         ->where('account_guid', $accountId)
    //         ->first()
    //         ->total
    //     );
    // }

    // public function getTotalByAccountType($accountType, $mixed)
    // {
    //     return floatval($this->applyTimeConstraints(DB::table('splits'), $mixed)
    //         ->select(DB::raw('IFNULL(SUM(value_num / value_denom), 0) AS total'))
    //         ->leftJoin('accounts', 'accounts.guid', '=', 'splits.account_guid')
    //         ->leftJoin('transactions', 'transactions.guid', '=', 'splits.tx_guid')
    //         ->where('account_type', $accountType)
    //         ->first()
    //         ->total
    //     );
    // }

    public function getFullInterval()
    {
        return DB::table('transactions')
            ->select(DB::raw('MIN(post_date) AS start'),
                     DB::raw('MAX(post_date) AS end'))
            ->first();
    }

    // public function getTransactionsByAccount($accountId)
    // {
    //     return DB::table('splits')
    //         ->leftJoin('transactions', 'transactions.guid', '=', 'splits.tx_guid')
    //         ->where('account_guid', $accountId)
    //         ->get();
    // }

    private function applyTimeConstraints($query, $mixed)
    {
        if ($mixed instanceof TimeInterval) {

            $query->whereBetween('post_date', [
                DateHelper::formatForSql($mixed->getStartDate()),
                DateHelper::formatForSql($mixed->getEndDate())
            ]);

        } else if ($mixed instanceof DateTime) {

            $query->where('post_date', '<=',
                DateHelper::formatForSql($mixed)
            );
        }

        return $query;
    }

    protected function createAccountsFromArray($accountArray)
    {
        $accounts = [];

        foreach ($accountArray as $account) {
            $accounts[$account->guid] = App::make('Account', [$account]);
        }

        return $accounts;
    }

    public function createTransactionsFromArray($transactionsArray)
    {
        $transactions = [];

        foreach($transactionsArray as $transaction) {
            $transactions[] = App::make('Transaction', [$transaction]);
        }

        return $transactions;
    }

    public function createTotalsFromArray($totalsArray)
    {
        $totals = [
            'ASSET'   => null,
            'BANK'    => null,
            'EQUITY'  => null,
            'EXPENSE' => null,
            'INCOME'  => null,
            'ROOT'    => null,
        ];

        foreach ($totalsArray as $total) {
            $totals[$total->account_type] = $total->total;
        }

        return $totals;
    }
}