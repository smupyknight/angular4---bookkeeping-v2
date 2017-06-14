'use strict';

function AuthCtrl( $scope, $state, AuthService, CRUDService, MiscService ) {

	$scope.errorMessage = [];

	$scope.signin = function() {
		AuthService.signin(
			{
				email: $scope.email,
				password: $scope.password
			},
			function( response ) {
				CRUDService.retrieve( 'company_profile' ).done( function( response ) {
					if ( response.data.content.length != 0 ) {
						sessionStorage.companyName = response.data.content[ 0 ].company_name;
						$state.go( 'app.main.dashboard' );
					}
				} );
				sessionStorage.access_token = response.data.content.access_token;
				toastr.success( 'Successfully signed in' );
			},
			function( response ) {
				$scope.errorMessages = response.data.content;
			}
		);
	}

	$scope.signup = function() {
		AuthService.signup(
			{
				name: $scope.name,
				email: $scope.email,
				password: $scope.password
			},
			function( response ) {
				toastr.success( 'Successfully signed up' );
				$state.go( 'app.user.signin' );
			},
			function( response ) {
				alert( JSON.stringify( response.data ) );
			}
		);
	}

	$scope.initialize = function() {
		CommonFunc().initializeValidation( 'form.form-horizontal' );
		CommonFunc().initializeCheckBox( '.chk-remember' );
		sessionStorage.userName = '';
		sessionStorage.companyName = '';
	}

	$scope.initialize();
}

angular
	.module( 'bookkeeping' )
	.controller( 'AuthCtrl', [ '$scope', '$state', 'AuthService', 'CRUDService', 'MiscService', AuthCtrl ] );