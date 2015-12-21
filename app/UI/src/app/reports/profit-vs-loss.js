(function() {
    'use strict';

    angular.module('reports.profit-vs-loss', [
        'ui.router',
        'ng-stack.services.title'
    ])
    .config(Config)
    .controller('ReportProfitVsLossCtrl', ReportProfitVsLossCtrl);


    Config.$inject = ['$stateProvider'];
    ReportProfitVsLossCtrl.$inject = ['titleService', 'ApiService'];


    function Config(stateProvider) {
        stateProvider.state('profit-vs-loss', {
            url: '/reports/profit-vs-loss',
            views: {
                main: {
                    controller: 'ReportProfitVsLossCtrl',
                    controllerAs: 'report',
                    templateUrl: 'reports/profit-vs-loss.tpl.html'
                }
            }
        });
    }


    function ReportProfitVsLossCtrl(titleService, apiService) {

        titleService.setTitle('Profit vs. Loss');

        var self = this;

        self.items = [];

        angular.extend(self,
        {
            resources: {
                periods: {
                    get: resourcesPeriodsGet,
                    getTitle: resourcesPeriodsGetTitle,
                    parse: resourcesPeriodsParse
                },
                accounts: {
                    get: resourcesAccountsGet,
                    parse: resourcesAccountsParse,
                    hash: resourcesAccountsHash
                }
            },
            events: {
                statistics: { load: eventsStatisticsLoad },
                revenues: { load: eventsRevenuesLoad },
                expenses: { load: eventsExpensesLoad }
            }
        });

        self.resources.periods.get();

        // self.resources.periods.get()
        function resourcesPeriodsGet()
        {
            apiService.getPeriods().then(
                function(response) {
                    self.items = self.resources.periods.parse(
                        response.data
                    );
                },
                function() {
                    console.log(arguments);
                }
            );
        }

        function resourcesAccountsGet(period, type)
        {
            return apiService.getAccounts({
                interval: period.code,
                account_type: type
            });
        }

        // self.resources.periods.parse()
        function resourcesPeriodsParse(periods) {

            return periods.map(function(period) {

                var item = {
                    title: self.resources.periods.getTitle(period.start),
                    total: period.totals.PROFIT.formatted,
                    children: []
                };

                item.children.push({
                    period: period,
                    title: 'Statistics',
                    children: [],
                    onLoad: self.events.statistics.load
                });

                item.children.push({
                    period: period,
                    title: 'Revenues',
                    total: period.totals.INCOME.formatted,
                    children: [],
                    onLoad: self.events.revenues.load
                });

                item.children.push({
                    period: period,
                    title: 'Expenses',
                    total: period.totals.EXPENSE.formatted,
                    children: [],
                    onLoad: self.events.expenses.load
                });

                return item;
            });
        }

        // self.resources.accounts.parse()
        function resourcesAccountsParse(accounts)
        {
            var items = [];

            for (var i in accounts) {

                var account = accounts[i];

                var item = {
                    title: account.name,
                    total: account.total.formatted,
                    children: []
                };

                for (var j in account.transactions) {
                    var transaction = account.transactions[j];
                    item.children.push({
                        title: transaction.description,
                        total: transaction.amount.formatted
                    });
                }

                item.children = angular.extend(item.children,
                    self.resources.accounts.parse(account.accounts)
                );

                items.push(item);
            }

            return items;
        }

        function resourcesAccountsHash(accountsArray)
        {
            var hash = {};

            for (var i in accountsArray) {
                hash[accountsArray[i].guid] = accountsArray[i];
            }

            return hash;
        }

        // self.resources.periods.getTitle()
        function resourcesPeriodsGetTitle(dateString)
        {
            var date = new Date(dateString);

            return [
                date.getFullYear(),
                apiService.getMonthName(date.getMonth())
            ]
            .join(' ');
        }

        function eventsStatisticsLoad(period)
        {
            alert('loading stats');
        }

        function eventsRevenuesLoad(item)
        {
            self.resources.accounts.get(item.period, 'INCOME')
            .then(
                function(response) {
                    item.children = self.resources.accounts.parse(
                        response.data.accounts
                    );
                },
                function() {
                    console.log(arguments);
                }
            );
        }

        function eventsExpensesLoad(item)
        {
            self.resources.accounts.get(item.period, 'EXPENSE')
            .then(
                function(response) {
                    item.children = self.resources.accounts.parse(
                        response.data.accounts
                    );
                },
                function() {
                    console.log(arguments);
                }
            );
        }
    }

}());