(function() {
    'use strict';

    angular.module('ng-stack.services.api', [])
    .factory('ApiService', ApiService);


    ApiService.$inject = ['$http'];


    function ApiService($http)
    {
        return {
            months: generateMonths(),
            getPeriods: getPeriods,
            getAccounts: getAccounts,
            getMonthName: getMonthName
        };

        function generateMonths()
        {
            return [
                "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio",
                "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
            ];
        }

        function getPeriods()
        {
            return $http(
            {
                url: '/api/periods',
                method: 'get',
                params: {
                    step: 'months',
                    length: 1
                }
            });
        }

        function getAccounts(params)
        {
            return $http(
            {
                url: '/api/periods/detail/type',
                method: 'get',
                params: params
            });
        }

        function getMonthName(month)
        {
            /* jshint validthis: true */
            return this.months[month];
        }
    }

}());