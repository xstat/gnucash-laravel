(function() {
    'use strict';

    /*
     * Config the routes for Home Module
     *
     * @param stateProvider
     * @constructor
     */
    function Config(stateProvider) {
      stateProvider.state('home', {
        url: '/',
        views: {
          main: {
            controller: 'HomeCtrl',
            controllerAs: 'HomeCtrl',
            templateUrl: 'home/home.tpl.html'
          }
        }
      });
    }


    /*
     * Home Controller for main view
     *
     * @param titleService
     * @constructor
     */
    function HomeCtrl(titleService) {
      titleService.setTitle('Home');
    }

    Config.$inject = ['$stateProvider'];

    HomeCtrl.$inject = ['titleService'];

    angular.module('ng-stack.controllers.home', ['ui.router', 'ng-stack.services.title'])
        .config(Config)
        .controller('HomeCtrl', HomeCtrl);
}());
