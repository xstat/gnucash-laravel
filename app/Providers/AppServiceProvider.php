<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\Ledger\Ledger;
use App\Models\Ledger\Account;
use App\Models\Ledger\Period as LedgerPeriod;

use App\Repositories\GnuCashRepository;

use App\Models\Time\Calculator as TimeCalculator;
use App\Models\Time\Interval as TimeInterval;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Ledger', function($app) {
            return new Ledger(
                $app['GncRepo']
            );
        });

        $this->app->singleton('CurrencyHelper', function($app) {
            return new \App\Helpers\Currency;
        });

        $this->app->singleton('GncRepo', function($app) {
            return new GnuCashRepository();
        });

        $this->app->bind('TimeCalculator', function($app, $args) {
            return new TimeCalculator(
                array_shift($args),
                array_shift($args)
            );
        });

        $this->app->bind('TimeInterval', function($app, $args) {
            return new TimeInterval(
                array_shift($args),
                array_shift($args)
            );
        });

        $this->app->bind('LedgerPeriod', function($app, $args) {
            return new LedgerPeriod(
                array_shift($args)
            );
        });

        $this->app->bind('Account', function($app, $args) {
            return new Account(
                array_shift($args)
            );
        });

        $this->app->bind('Transaction', function($app, $args) {
            return new \App\Models\Ledger\Transaction(
                array_shift($args)
            );
        });
    }
}
