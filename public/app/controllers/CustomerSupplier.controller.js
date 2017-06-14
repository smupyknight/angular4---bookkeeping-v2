'use strict';

function CustomerSupplierCtrl( $compile, $scope, $state, CRUDService )
{
	$scope.person = {};

	$scope.getList = function() {
		CRUDService.retrieve( $scope.targetTableName ).done(function(response) {
			CommonFunc().redrawDataTable( $scope.dataTable, response.data.content, $scope.generateRow, $scope.targetTableName );
		} );
	}

    $scope.generateRow = function( row, i, tableName ) {
		return [ row.id, row.name, row.address1, row.phone, row.email, row.balance, CommonFunc().actionRow( row, tableName ) ];
	}

    $scope.goToNewInvoice = function( personName ) {
    	sessionStorage.personName = personName;
    	$state.go( 'app.main.sales.new-invoice' );
    }

    $scope.goToNewPayment = function( personName ) {
        sessionStorage.personName = personName;
        $state.go( 'app.main.sales.new-payment' );
    }

    $scope.goToNewSalesReceipt = function( personName ) {
        sessionStorage.personName = personName;
        $state.go( 'app.main.sales.new-sales-receipt' );
    }

    $scope.goToNewExpense = function( personName ) {
    	sessionStorage.personName = personName;
    	$state.go( 'app.main.expense.new-expense' );
    }

    $scope.initialize = function() {
        $scope.dataTable = CommonFunc().initializeDataTable( '#datatable', [ "ID", "Name", "Address", "Phone", "Email", "Balance", "Action" ], $scope, $compile );
        $scope.getList();
    }

    setTimeout( function() {
        $scope.initialize();
    }, 0 );
}

angular
	.module( 'bookkeeping' )
	.controller( 'CustomerSupplierCtrl', [ '$compile', '$scope', '$state', 'CRUDService', CustomerSupplierCtrl ] );