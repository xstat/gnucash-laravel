function TitleService(document) {
  var suffix = '';

  return {
    setSuffix: function(s) {
      suffix = s;
    },

    getSuffix: function() {
      return suffix;
    },

    /*
     * Set document's title
     *
     * @param title: string
     */
    setTitle: function(t) {
      var title;

      if (suffix !== '') {
        title = t + suffix;
      } else {
        title = t;
      }

      document.prop('title', title);
    },

    /*
     * Get document's title
     *
     * @return: string
     */
    getTitle: function() {
      return document.prop('title');
    }
  };
}

TitleService.$inject = ['$document'];

angular.module('ng-stack.services.title', [])
    .factory('titleService', TitleService);
