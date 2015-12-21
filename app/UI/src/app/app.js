(function(angular) {
    'use strict';
    /*
     * Configuration function for the app
     *
     * @param httpProvider
     * @param locationProvider
     * @param urlRouterProvider
     * @constructor
     */
    function Config(httpProvider, locationProvider, urlRouterProvider) {
      //uncomment if you want to use html5 mode.
      // locationProvider.html5Mode(true).hashPrefix('!');

      httpProvider.defaults.useXDomain = true;
      delete httpProvider.defaults.headers.common['X-Requested-With'];

      urlRouterProvider.otherwise('/');
    }


    /*
     * Main Controller for the App
     *
     * @param scope
     * @param UserData
     * @param rootScope
     * @constructor
     */
    function AppCtrl(rootScope) {
        // TODO: Do something meaningful here
    }

    function run(titleService) {
      titleService.setSuffix(' | App Name');
    }

    Config.$inject = ['$httpProvider', '$locationProvider', '$urlRouterProvider'];

    AppCtrl.$inject = ['$rootScope'];

    run.$inject = ['titleService'];

    angular.module('ng-stack',
        [
            'templates-app',
            'templates-common',
            'ui.bootstrap',
            'ui.router',
            'ng-stack.controllers.home',
            'ng-stack.services.title',
            'ng-stack.services.api',
            'reports.profit-vs-loss'
        ]
    )
        .config(Config)
        .run(run)
        .controller('AppCtrl', AppCtrl);
}(angular));
