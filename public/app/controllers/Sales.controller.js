'use strict';

function SalesCtrl( $scope, $compile, $state, CRUDService, MiscService ) {

	$scope.customers = [];

	$scope.generateSalesRow = function( row ) {
		var transactionTypes = [ '', 'Invoice', 'Payment', 'Sales Receipt' ];
		var statues = [ [], [ '', 'Unpaid', 'Partial', 'Paid' ], [ '', 'Unapplied', 'Partial', 'Closed' ], [ '', 'Paid' ], [ '', 'Paid' ] ];
		var total = row.transaction_type != 2 ? row.total : -row.total;
		var balance = row.transaction_type != 2 ? row.balance: -row.balance;
		var action = statues[ row.transaction_type ][ row.status ];
		if ( row.transaction_type == 1 && row.status == 1 ) {
			action = '<a href="" data-ng-click="goToPayment(' + row.invoice_receipt_no + ')"> Unpaid </a>';
		}
		return [ row.id, row.date, transactionTypes[ row.transaction_type ], row.invoice_receipt_no, $scope.customers[ row.customer_id ], row.transaction_type != 2 ? row.due_date : '', total, balance, action ];
	}

	$scope.goToPayment = function( invoiceId ) {
		sessionStorage.invoiceId = invoiceId;
		$state.go( 'app.main.sales.new-payment' );
	}

	$scope.initialize = function() {
		$scope.dataTable = CommonFunc().initializeDataTable( '#salesDataTable', [ "ID", "Date", "Type", "#", "Customer", "Due Date", "Total", "Balance", "Status" ], $scope, $compile );

		$( '#salesDataTable' ).on( 'click', 'tbody tr td:nth-child(9)', function( event ) {
			event.stopPropagation();
		} );

		$( '#salesDataTable' ).on( 'click', 'tbody tr', function() {
			CommonFunc().goToTransaction( $( this ).find( 'td:nth-child(1)' ).text(), $( this ).find( 'td:nth-child(3)' ).text().replace( ' ', '-' ).toLowerCase(), $state );
		} );

		MiscService.massRetrieve( [ 'customer', 'sales' ] ).done( function( response ) {
			var content = response.data.content;

			$scope.customers = MiscService.extractUniqueNameList( content.customer );
			CommonFunc().redrawDataTable( $scope.dataTable, content.sales, $scope.generateSalesRow, 'sales' );
		} );
	}

	$scope.initialize();
}

angular
	.module( 'bookkeeping' )
	.controller( 'SalesCtrl', [ '$scope', '$compile', '$state', 'CRUDService', 'MiscService', SalesCtrl ] );