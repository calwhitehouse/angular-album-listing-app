
(function () {
    'use strict';
    /** Service to return the data */

    angular.module('AlbumApp').
    service('dataService',         // the data service name, can be anything we want
        ['$q',                     // dependency, $q handles promises, the request initially returns a promise, not the data
            '$http',                  // dependency, $http handles the ajax request
            function($q, $http) {

                /**
                 * var to hold the data base url
                 */
                var urlBase = '/cm0665-assignment/server/index.php';
                var user = null;
                /*
                 * method to retrieve albums, or, more accurately a promise which when
                 * fulfilled calls the success method
                 */
                this.getAlbums = function () {
                    var defer = $q.defer(),             // The promise
                        data = {
                            action: 'list',
                            subject: 'albums',

                        };
                    /**
                     * make an ajax get call
                     * chain calls to .success and .error which will resolve or reject the promise
                     * @param {string} urlBase The url to call, later we'll to this to pass parameters
                     * @param {object} config a configuration object, can contain parameters to pass, in this case we set cache to true
                     * @return {object} promise The call returns, not data, but a promise which only if the call is successful is 'honoured'
                     */
                    $http.get(urlBase, {params: data, cache: false}).
                    success(function(response){
                        defer.resolve({
                            data: response.ResultSet.Result,  // create data property with value from response
                            rowCount: response.ResultSet.RowCount  // create rowCount property with value from response
                        });
                    }).
                    error(function(err){
                        defer.reject(err);
                    });
                    return defer.promise;
                };
                /**
                 * To display albums by genre
                 * @param genre_id
                 * @returns {object} promise
                 */
                this.getAlbumsByGenre = function (genre_id) {
                    var defer = $q.defer(),
                        data = {
                            action: 'search',
                            subject: 'genre',
                            id: genre_id
                        };
                    $http.get(urlBase, {params: data, cache: false}).
                    success(function(response){
                        defer.resolve({
                            data: response.ResultSet.Result,
                            rowCount: response.ResultSet.RowCount
                        });
                    }).
                    error(function(err){
                        defer.reject(err);
                    });
                    return defer.promise;
                };
				/**
				 * To display the track listing
				 * @param {string} album_id The album id that has been selected by the user
				 * @returns {object} promise
				 */
				this.getTracks = function (album_id) {
					var defer = $q.defer(),
						data = {
							action: 'list',
							subject: 'tracks',
							id: album_id
						};
					$http.get(urlBase , {params: data, cache: false}).
					success(function(response){
						defer.resolve({
							data: response.ResultSet.Result,
							rowCount: response.ResultSet.RowCount
						});
					}).
					error(function(err){
						defer.reject(err);
					});
					return defer.promise;
				};
                /**
                 * List of genres
                 * @returns {object} promise
                 */
				this.getGenre = function () {
					var defer = $q.defer(),
						data = {
							action: 'list',
							subject: 'genre',
						};

					$http.get(urlBase , {params: data, cache: false}).
					success(function(response){
						defer.resolve({
							data: response.ResultSet.Result,
							rowCount: response.ResultSet.RowCount
						});
					}).
					error(function(err){
						defer.reject(err);
					});
					return defer.promise;
				};
                /**
                 * Search albums using term submitted
                 * @param {string} term
                 * @returns {object} promise
                 */
                this.searchAlbums = function (term) {
                    var defer = $q.defer(),
                        data = {
                            action: 'search',
                            subject: 'albums',
                            data: term
                        };

                    $http.get(urlBase , {params: data, cache: false}).
                    success(function(response){
                        defer.resolve({
                            data: response.ResultSet.Result,
                            rowCount: response.ResultSet.RowCount
                        });
                    }).
                    error(function(err){
                        defer.reject(err);
                    });
                    return defer.promise;
                };
                /**
                 * Used to display notes
                 * @param {number} album_id
                 * @returns {object} promise
                 */
                this.getNotes = function (album_id) {
                    var defer = $q.defer(),
                        data = {
                            action: 'show',
                            subject: 'notes',
                            id: album_id
                        };

                    $http.get(urlBase , {params: data, cache: false}).
                    success(function(response){
                        defer.resolve({
                            data: response.ResultSet.Result,
                            rowCount: response.ResultSet.RowCount
                        });
                    }).
                    error(function(err){
                        defer.reject(err);
                    });
                    return defer.promise;
                };
                /**
                 * Update existing note
                 * @param {number} album_id
                 * @param {string} notes
                 * @returns {object} promise
                 */
                this.updateNote = function (album_id, notes) {
                    var defer = $q.defer(),
                        data = {
                            action: 'update',
                            subject: 'notes',
                            data: angular.toJson({
                                notes: notes,
                                album_id: album_id
                            })
                        };

                    $http.post(urlBase, data).
                    success(function(response){
                        defer.resolve(response);
                    }).
                    error(function (err){
                        defer.reject(err);
                    });
                    return defer.promise;
                };
                /**
                 * Add a new note
                 * @param {number} album_id
                 * @param {string} notes
                 * @returns {object} promise
                 */
                this.addNote = function (album_id, notes) {
                    var defer = $q.defer(),
                        data = {
                            action: 'add',
                            subject: 'note',
                            data: angular.toJson({
                                notes: notes,
                                album_id: album_id
                            })
                        };

                    $http.post(urlBase, data).
                    success(function(response){
                        defer.resolve(response);
                    }).
                    error(function (err){
                        defer.reject(err);
                    });
                    return defer.promise;
                };
                /**
                 * Login user
                 * @param {string} user
                 * @returns {object} promise
                 */
                this.loginUser = function(user) {
                    var defer = $q.defer(),
                        data = {
                            //XDEBUG_SESSION_START: true,
                            action: 'login',
                            subject: 'user',
                            data: angular.toJson(user)
                        };
                    $http.post(urlBase, data).
                    success(function(response){
                        defer.resolve({
                            status: response.status,
                            user: response.ResultSet.Result
                        });

                    }).                                         // another dot to chain to error()
                    error(function(err){
                        defer.reject(err);
                    });
                    return defer.promise;
                };
                /**
                 * Logged in status of user
                 * @param {string} user
                 * @returns {object} promise
                 */
                this.getLoggedInStatus = function(user) {
                    var defer = $q.defer(),
                        data = {
                            action: 'login',
                            subject: 'status',

                        };
                    $http.post(urlBase, data).
                    success(function(response){
                        defer.resolve({
                            status: response.status,
                            user: response
                        });

                    }).                                         // another dot to chain to error()
                    error(function(err){
                        defer.reject(err);
                    });
                    return defer.promise;
                };
                /**
                 * Logout User
                 * @param {string} user
                 * @returns {object} promise
                 */
                this.logoutUser = function(user) {
                    var defer = $q.defer(),
                        data = {
                            action: 'logout',
                            subject: 'user',

                        };
                    $http.post(urlBase, data).
                    success(function(response){
                        defer.resolve({
                            status: response.status,
                            user: response
                        });

                    }).
                        error(function(err){
                        defer.reject(err);
                    });
                    return defer.promise;
                };

            }
        ]
    ).
    service('applicationData' ,
        /**
         * @param $rootScope
         */
        function ($rootScope) {
            var sharedService = {};
            sharedService.info = {};
            sharedService.publishInfo = function(key, obj){
                this.info[key] = obj;
                $rootScope.$broadcast('systemInfo_' +key, obj);
            };

            return sharedService;
        }
    );
}());
