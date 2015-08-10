"use strict";
var reang;
var $ = jQuery;
reang = angular.module('reang', ['ngResource', 'ui.router'])
.filter('to_trusted', ['$sce', function($sce) {
	return function(text) {
		return $sce.trustAsHtml(text);
	};
}])
.config(function($stateProvider, $urlRouterProvider) {
	// console.log( $stateProvider );
	$urlRouterProvider.otherwise('/');
	$stateProvider
	.state('list', {
		url: '/',
		views: {
			"content-view": { 
				template: '<reactposts data="posts" id="test"></reactposts>',
				controller: 'reang_controller'
			},
			"main-nav": {
				template: '<reactmainnav data="nav_items" id="nav_items"></react-main-nav>',
				controller: 'reang_nav_controller'
			},
		}
	})
	.state('single', {
		url: '/post/:id',
		views: {
			"content-view": { 
				template: '<reactposts data="posts" id="test"></reactposts>',
				controller: function( $scope, $stateParams, Posts ) {
					$scope.post_id = $stateParams.id;
					$scope.getPosts = function(){
						Posts.get({ID: $stateParams.id}, function(res){
							$scope.posts = res;
						});
					}
					$scope.getPosts();
				}
			},
			"main-nav": {
				template: '<reactmainnav data="nav_items" id="nav_items"></react-main-nav>',
				controller: 'reang_nav_controller'
			},
		}
	}).state('page', {
		url: '/pages/:id',
		views: {
			"content-view": { 
				template: '<reactposts data="page" id="page"></reactposts>',
				controller: function( $scope, $stateParams, Page ) {
					$scope.post_id = $stateParams.id;
					$scope.getPage = function(){
						Page.get({ID: $stateParams.id}, function(res){
							$scope.page = res;
						});
					}
					$scope.getPage();
				}
			},
			"main-nav": {
				template: '<reactmainnav data="nav_items" id="nav_items"></react-main-nav>',
				controller: 'reang_nav_controller'
			},
		}
	});
	//$locationProvider.html5Mode(true);
})
.run(function($rootScope, $location){
	$rootScope.$on("$routeChangeStart", function (event, next, current) {
		console.log( next, current );
	});
	$rootScope.$on("$stateChangeSuccess", function(evt, to, toP, from, fromP) { console.log('Success to:', to.url ); });
	$rootScope.$on("$stateChangeError", function(evt, to, toP, from, fromP, error) { console.log('Error:', to.url, error ); });
	$rootScope.$on("$stateNotFound", function(evt, unfoundState, fromState, fromParams) { console.log('Not Found:', unfoundState ); });
})
.factory('Posts', function($resource) {
	return $resource(ajaxInfo.json_url + 'wp/v2/posts/:ID?_wp_json_nonce='+ajaxInfo.nonce, {
		ID: '@ID'
	},{
        'update': { ID: '@ID', method: 'PUT' }
    });
})
.factory('Page', function($resource) {
	return $resource(ajaxInfo.json_url + 'wp/v2/pages/:ID?_wp_json_nonce='+ajaxInfo.nonce, {
		ID: '@ID'
	},{
        'update': { ID: '@ID', method: 'PUT' }
    });
})
.factory('MainNavigation', function($resource) {
	return $resource(ajaxInfo.json_url + 'wp-reaction/v1/menu-location/main_nav?_wp_json_nonce='+ajaxInfo.nonce );
})
.controller( 'reang_controller', ['$rootScope', '$scope', 'Posts', function($rootScope, $scope, Posts){
	// console.log( 'test' );
	$scope.getPosts = function(){
		Posts.query({}, function(res){
			$scope.posts = res;
		});
	}
	$scope.getPosts();
	$('body').on('click', '.edit_post', function(e) {
		var post_id = $(this).data('id');
		console.log($scope);
		
		Posts.get({ID:post_id}, function(res){
			$scope.editPost = res;
		})
		
	})
	
	$scope.savePost = function() {
		console.log('saving..', $scope.editPost);
		$scope.editPost.content_raw = $scope.editPost.content.rendered;
		Posts.update({ID:$scope.editPost.id}, function(res){
			$scope.getPosts();
			$('#editPost').hide();
			$('.modal-backdrop').hide();
		});
	}
	
}])
.controller( 'reang_nav_controller', ['$rootScope', '$scope', 'MainNavigation', function($rootScope, $scope, MainNavigation){
	// query the nav items
	$scope.getMainNav = function(){
		MainNavigation.query({}, function(res) {
			$scope.nav_items = res;
		});
	}
	$scope.getMainNav();	
}])
.directive('reactposts', function($rootScope) {
	return {
		restrict: 'E',
		scope: { data: '=', id: '@' },
		link: function($scope,elm,attrs) {
			$scope.$watch('data', function(n,o){
				if( n && n.length ) {
					$rootScope.react_app = React.render(
						React.createElement(APP, {data:$scope.data}),
						elm[0]
					)
				}
				if( n && n.id ) {
					$rootScope.react_app = React.render(
						React.createElement(APP, {data:$scope.data}),
						elm[0]
					)
				}
			});
		}
	}
})
.directive('reactmainnav', function($rootScope) {
	return {
		scope: { data: '=', id: '@' },
		link: function($scope,elm,attrs) {
			$scope.$watch('data', function(n,o) {
				if( n && n.length ) {
					$rootScope.react_app = React.render(
						React.createElement(MainNavigation, {data:$scope.data}),
						elm[0]
					)
				}
			});
		}
	}
})
.controller( 'editPostCtrl', ['$rootScope', '$scope', 'Posts', function($rootScope, $scope, Posts) {
	console.log('editing..');
	
	$('body').on('click', '.edit_post', function(e) {
		var post_id = $(this).data('id');
		
		Posts.get({ID:post_id}, function(res){
			$scope.editPost = res;
		});
		
	})
	
	$scope.savePost = function() {
		console.log('saving..', $scope.editPost.id);
		$scope.editPost.content_raw = $scope.editPost.content.rendered;
		Posts.update({ID:$scope.editPost.id}, function(res){
			Posts.query({}, function(res){
				$scope.getPosts();
				$('#editPost').hide();
				$('.modal-backdrop').hide();
			})
		});
	}
}]);