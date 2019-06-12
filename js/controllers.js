(function () {
    "use strict";

    /**
     * Extend the module 'CourseApp' instantiated in app.js  To add a controller called
     * IndexController (on line 17) and CourseController (on line 26)
     *
     *
     * The controller is given two parameters, a name as a string and an array.
     * The array lists any injected objects to add and then a function which will be the
     * controller object and can contain properties and methods.
     * $scope is a built in object which refers to the application model and acts
     * as a sort of link between the controller, its data and the application's views.
     * '
     * @link https://docs.angularjs.org/guide/scope
     */
    angular.module('AlbumApp').
    controller('IndexController',   // controller given two params, a name and an array
        [
            '$scope',
            'dataService',
            'applicationData',// angular variable as a string
            function ($scope, dataService, appData) {

            }
        ]
    ).
    /**
     * Genre list, album information, filter album by genre and search
     * @param $scope
     * @param dataService
     * @param appData
     * @param $location
     * @param $routeParams
     */
    controller('AlbumController',
        [
            '$scope',
            'dataService',
            'applicationData',
            '$location',
            '$routeParams',
            function ($scope, dataService, appData, $location, $routeParams) {
                $scope.genres = [ ];
                $scope.albums = [ ];
                /**
                 * Receives list of genres and binds data to $scope.genres
                 */
                var getGenre = function () {
                    dataService.getGenre().then( // then() is called when the promise is resolved or rejected
                        function (response) {
                            $scope.genres = response.data;
                        },
                        function (err){
                            $scope.status = 'Unable to load data ' + err;
                        }
                    );  // end of getGenre().then
                };
                getGenre(); //call the method

                appData.publishInfo('album', {});
                /**
                 * Receives list of albums and binds data to $scope.albumCount and $scope.albums
                 */
                var getAlbums = function () {
                    dataService.getAlbums().then(
                        function(response){
                            $scope.albumCount  = response.rowCount + ' albums';
                            $scope.albums      = response.data;
                            $scope.subTitle = 'Album Listing';
                        },
                        function(err){
                            $scope.status = 'Unable to load data ' + err;
                        },
                        function(notify){
                            console.log(notify);
                        }
                    ); // end of getAlbums().then
                };
				var albumInfo = $location.path().substr(1).split('/');
				if (albumInfo.length === 2) {
					// use the album id from the path and assign to
					// selectedAlbum so if the page is reloaded it's highlighted
					$scope.selectedAlbum = {album_id: albumInfo[1]};
				}

				$scope.selectAlbum = function ($event, album) {
					$location.path('/albums/' + album.album_id);
					$scope.selectedAlbum = album;
					appData.publishInfo('album', album);
				};
                getAlbums();  // call the method
                /**
                 * Receives genre id then binds albums matching that genre to $scope.albums
                 * @param genre_id
                 */
                $scope.getAlbumsByGenre = function (genre_id) {
                    dataService.getAlbumsByGenre(genre_id).then(
                        function(response){
                            $scope.albumCount  = response.rowCount + ' albums';
                            $scope.albums      = response.data;
                        },
                        function(err){
                            $scope.status = 'Unable to load data ' + err;
                        },
                        function(notify){
                            console.log(notify);
                        }
                    ); // end of getAlbumsByGenre().then
                };
                //if there's a genre id call method
                if ($routeParams && $routeParams.genre_id) {
                    getAlbumsByGenre($routeParams.genre_id);
                }
                /**
                 * Receives term the binds albums and artists matching term to $scope.albums
                 * @param term
                 */
                $scope.searchAlbums = function (term) {
                    dataService.searchAlbums(term).then(
                        function(response){
                            $scope.albumCount  = response.rowCount + ' albums';
                            $scope.albums      = response.data;
                        },
                        function(err){
                            $scope.status = 'Unable to load data ' + err;
                        },
                        function(notify){
                            console.log(notify);
                        }
                    ); // end of searchAlbums().then
                };
                //if there's a term call method
                if ($routeParams && $routeParams.term) {
                    searchAlbums($routeParams.term);
                }

            }
        ]
    ).
    /**
     * Controls track listing, notes (get and update)
     * @param $scope
     * @param dataService
     * @param $routeParams
     */
	controller('AlbumTracksController',
	   [
		   '$scope',
		   'dataService',
		   '$routeParams',
		   function ($scope, dataService, $routeParams){
			   $scope.tracks = [ ];
			   $scope.trackCount = 0;
			   $scope.artwork = "";
			   $scope.notes = [ ];
			   $scope.toggledEdit = false;
               /**
                * albumsid to get track list and bind response to $scope.tracks
                * deals with no artwork by binding default image to $scope.artwork
                * @param albumsid
                */
			   var getTracks = function (albumsid) {
				   dataService.getTracks(albumsid).then(
					   function (response) {
						   $scope.trackCount = response.rowCount + ' tracks';
						   $scope.tracks = response.data;
						   if (response.data[0].artwork == null) {
						   		$scope.artwork = "no-image.jpg";
						   } else {
						       $scope.artwork = response.data[0].artwork;
                           }
					   },
					   function (err){
						   $scope.status = 'Unable to load data ' + err;
					   }
				   );  // end of getTracks()
			   };
			   // only if there has been a albumid passed in do we bother trying to get the students
			   if ($routeParams && $routeParams.albumsid) {
				   getTracks($routeParams.albumsid);
			   }
               /**
                * albumsid to get note, response binds to $scope.notes
                * @param albumsid
                */
               var getNotes = function (albumsid) {
                   dataService.getNotes(albumsid).then(
                       function (response) {
                           $scope.notes = response.data[0].note
                       },
                       function (err){
                           $scope.status = 'Unable to load data ' + err;
                       }
                   );  // end of getNotes().then
               };
               // call getNotes only if there has been a albumid passed in
               if ($routeParams && $routeParams.albumsid) {
                   getNotes($routeParams.albumsid);
               }
               /**
                * Note and albumsid to add new note, response binds to $scope.notes
                * @param note
                */
               $scope.addNote = function (note) {
                   dataService.addNote($routeParams.albumsid, note).then(
                       function (response) {
                           $scope.notes = response;
                       },
                       function (err){
                           $scope.status = 'Unable to load data ' + err;
                       }
                   );  // end of getNotes()
               };
               /**
                * Note and albumsid to update note, response binds to $scope.notes
                * @param note
                */
               $scope.updateNote = function (note) {
                   dataService.updateNote($routeParams.albumsid, note).then(
                       function (response) {
                           $scope.notes = response;
                           $scope.toggledEdit = false;
                       },
                       function (err){
                           $scope.status = 'Unable to load data ' + err;
                       }
                   );  // end of updateNote().then
               };
               //boolean function so edit form can be displayed or hidden in the view
               $scope.toggleEditNote = function (toggle) {
                $scope.toggledEdit = toggle;
               };


		   }
		]
	   ).
       /**
       * Controls login, logged in status and logout
       * @param $scope
       * @param dataService
       */
	   controller('LoginController',  // create a LoginController
        [
            '$scope',
            'dataService',
            function ($scope, dataService ) {
                $scope.user = {};
                $scope.loggedInUser = null;
                /**
                 * Binds login response to dataService.user
                 */
                $scope.loginUser = function() {
                    dataService.loginUser($scope.user).then(
                        function (response){
                            if (response.status !== "error") {
                                dataService.user = response.user;
                                $scope.status = response.status;
                                /**
                                 * Binds logged in status to $scope.loggedInUser after login
                                 */
                                dataService.getLoggedInStatus().then(
                                    function (response) {
                                        $scope.loggedInUser = response.user;

                                    }
                                );

                            }
                        }
                    )
                }
                /**
                 * Binds logged in status to $scope.loggedInUser
                 */
                dataService.getLoggedInStatus().then(
                    function (response) {
                        $scope.loggedInUser = response.user;

                    }
                );
                /**
                 * Removes value from dataService.user and $scope.loggedInUser for logout
                 */
                $scope.logoutUser = function() {
                    dataService.logoutUser().then(
                        function (response) {
                            dataService.user = '';
                            $scope.loggedInUser = dataService.user;

                        }
                    )
                }
            }
        ]
    );
}());
