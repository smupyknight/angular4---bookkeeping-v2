'use strict';

function TableReportCtrl( $scope, RprtService ) {
	$scope.period = 'This year to date';
	$scope.report = {};

	$scope.retrieveReportByPeriod = function() {
        RprtService.retrieveReport( '/table/' + $scope.reportEndPoint, $scope.period ).then( function( response ) {
            $scope.report = response.data.content;
        } );
    }

    $scope.retrieveReportByFromTo = function() {
        RprtService.retrieveReport( '/table/' + $scope.reportEndPoint, $scope.report.dateFrom, $scope.report.dateTo ).then( function( response ) {
            $scope.report = response.data.content;
        } );
    }

    $scope.initialize = function() {
        CommonFunc().initializeDatePicker( '.dp-month-year', $scope );

        setTimeout( function() {
        	$scope.retrieveReportByPeriod();
        }, 10 );
    }

    $scope.initialize();
}

angular
	.module( 'bookkeeping' )
	.controller( 'TableReportCtrl', [ '$scope', 'RprtService', TableReportCtrl ] );