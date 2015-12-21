(function() {
    'use strict';

    angular.module('reports.profit-vs-loss')
    .directive('nestedTable', nestedTable);

    function nestedTable() {

        var directive = {
            link: link,
            scope: {items: '='},
            templateUrl: 'directives/nested-table/table.tpl.html',
            restrict: 'EA'
        };

        return directive;

        function link(scope, element, attrs) {

            scope.rowTemplate = 'directives/nested-table/row.tpl.html';

            angular.extend(scope, {
                events: {
                    titleClick: eventsTitleClick
                },
                utils: {
                    hasTotal: utilsHasTotal,
                    hasChildren: utilsHasChildren,
                    getChildren: utilsGetChildren,
                    parentHasTotal: utilsParentHasTotal
                }
            });

            function eventsTitleClick(item)
            {
                item.expanded = !item.expanded;

                if (item.onLoad && !item.loaded) {
                    item.onLoad(item);
                    item.loaded = true;
                }
            }

            // scope.utils.hasTotal
            function utilsHasTotal(item)
            {
                return angular.isDefined(item.total);
            }

            // scope.utils.hasChildren
            function utilsHasChildren(item)
            {
                return angular.isArray(item.children);
            }

            // scope.utils.getChildren
            function utilsGetChildren(item)
            {
                if (angular.isArray(item.children)) {

                    if (!item.linked) {
                        angular.forEach(item.children, function(child) {
                            child.parent = item;
                        });
                        item.linked = true;
                    }

                    return item.children;
                }

                return [];
            }

            // scope.utils.parentHasTotal
            function utilsParentHasTotal(item)
            {
                var parent = item.parent || {};
                return scope.utils.hasTotal(item.parent);
            }
        }
    }

}());