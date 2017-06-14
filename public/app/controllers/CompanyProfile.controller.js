'use strict';

function CompanyProfileCtrl( $scope, $window, CRUDService ) {

	$scope.company = {};
	$scope.createOrUpdate = 'create';

	$scope.getCompanyProfile = function() {
		CRUDService.retrieve( 'company_profile' ).done( function( response ) {
			if ( response.data.content.length != 0 ) {
				$scope.company = response.data.content[ 0 ];
				$scope.company.tableName = 'company_profile';
				$scope.createOrUpdate = 'update';
			}
		} );
	}

	$scope.setCompanyProfile = function() {
		if ( $scope.createOrUpdate == 'create' ) {
			CRUDService.create( $scope.company ).done( function( response ) {
				$scope.errorMessages = [];
				$scope.createOrUpdate = 'update';
				toastr.success( 'Successfully Saved' );
				sessionStorage.companyName = $scope.company.company_name;
			} ).fail( function( response ) {
				$scope.errorMessages = response.data.content;
			} );
		} else {
			CRUDService.update( 1, $scope.company ).done( function( response ) {
				$scope.errorMessages = [];
				toastr.success( 'Successfully Updated' );
				sessionStorage.companyName = $scope.company.company_name;
			} ).fail( function( response ) {
				$scope.errorMessages = response.data.content;
			} );
		}
	}

	$scope.goBack = function() {
		$window.history.back();
	}

	$scope.initialize = function() {
		$scope.getCompanyProfile();
		CommonFunc().initializeDropzone( '#dpz-single-file' );
		CommonFunc().initializeValidation( 'form.form', function( $form, errors ) {
			$scope.setCompanyProfile();
		} );
	}

	$scope.initialize();
}

angular
	.module( 'bookkeeping' )
	.controller( 'CompanyProfileCtrl', [ '$scope', '$window', 'CRUDService', CompanyProfileCtrl ] );