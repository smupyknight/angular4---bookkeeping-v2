'use strict';

function ExpenseCtrl( $scope, $state, $compile, CRUDService, MiscService ) {

	$scope.accounts = [];
	$scope.payees = [];
	$scope.customers = [];
	$scope.suppliers = [];

	$scope.pageName = 'New Transaction';

	$scope.setPayeeType = function( data, type ) {
		for ( var i = 0; i < data.length; i++ ) {
			data[ i ].type = type;
		}
		return data;
	}

	$scope.generateExpenseRow = function( row ) {
		var transaction_types = [ '', '', '', '', 'Expense' ];
		var payee_id = row.payee_type == 1 ? row.payee_id : parseInt( row.payee_id ) + parseInt( $scope.customers.slice( -1 )[ 0 ].id ) + 1;
		return [ row.id, row.date, transaction_types[ row.transaction_type ], row.id, $scope.payees[ payee_id ].name, $scope.accounts[ row.account_id ], row.total ];
	}

	$scope.initialize = function() {
		$scope.dataTable = CommonFunc().initializeDataTable( '#expenseDataTable', [ "ID", "Data", "Type", "#", "Payee", "Category", "Total" ], $scope, $compile );

		$( '#expenseDataTable' ).on( 'click', 'tbody tr', function() {
			CommonFunc().goToTransaction( $( this ).find( 'td:nth-child(1)' ).text(), $( this ).find( 'td:nth-child(3)' ).text(), $state );
		} );

		MiscService.massRetrieve( [ 'account', 'customer', 'supplier', 'expense' ] ).done( function( response ) {
			var content = response.data.content;

			$scope.accounts = MiscService.extractUniqueNameList( content.account );
			$scope.customers = $scope.setPayeeType( MiscService.extractUniqueList( content.customer ), 1 );
			$scope.suppliers = $scope.setPayeeType( MiscService.extractUniqueList( content.supplier ), 2 );
			$scope.payees = $scope.customers.concat( $scope.suppliers );
			$scope.expenses = content.expense;

			CommonFunc().redrawDataTable( $scope.dataTable, $scope.expenses, $scope.generateExpenseRow, 'expense' );
		} );
	}

	$scope.initialize();
}

angular
	.module( 'bookkeeping' )
	.controller( 'ExpenseCtrl', [ '$scope', '$state', '$compile', 'CRUDService', 'MiscService', ExpenseCtrl ] );