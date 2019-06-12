(function () {
    "use strict";  // turn on javascript strict syntax mode

    /**
     * Start a new Application, a module in Angular
     * @param {string} ApplicationName a string which will be the name of the application
     *                 and an object to which other components are added
     * @param {array} dependencies An array of dependencies, the names are passed as strings
     */
    angular.module("AlbumApp",
        [
            'ngRoute'   // the only dependency at this stage, for routing
        ]
    ).              // chain the call to config
    config(
        [
            '$routeProvider',     // built in variable which injects functionality, passed as a string
            function($routeProvider) {
                $routeProvider.
                when('/albums', {
                    controller: 'AlbumController'
                }).
                when('/albums/:albumsid', {
                    templateUrl: 'js/partials/track-list.html',
                    controller: 'AlbumTracksController'
                }).

                otherwise({
                    redirectTo: '/'
                });
            }
        ]
    ).  // end of config method
    /**
     * Filter to encode image URI
     */
	filter('imageURI', function(){
			return function(uri){
				return encodeURI(uri)
			}

	}).
        //Referenced from yrezgui at http://jsfiddle.net/yrezgui/34fnp/ [Accessed 23/4/18]
    filter('Filesize', function () {
            return function (size) {
                if (isNaN(size))
                    size = 0;

                if (size < 1024)
                    return size + ' Bytes';

                size /= 1024;

                if (size < 1024)
                    return size.toFixed(2) + ' Kb';

                size /= 1024;

                if (size < 1024)
                    return size.toFixed(2) + ' Mb';

                size /= 1024;

                if (size < 1024)
                    return size.toFixed(2) + ' Gb';


            }});
}());   // end of IIFE
