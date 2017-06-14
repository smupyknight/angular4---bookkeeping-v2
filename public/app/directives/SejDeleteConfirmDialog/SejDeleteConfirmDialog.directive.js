'use strict';

function SejDeleteConfirmDialog( $state, TrxnService ) {

	var output = {};

	output.transclude = true;
	output.templateUrl = 'app/directives/SejDeleteConfirmDialog/SejDeleteConfirmDialog.html';
	output.link = function( scope, element, attrs ) {

		scope.targetName = attrs.targetName;

		scope.showDeleteTransactionDialog = function() {
			if ( sessionStorage.transactionId > 0 ) {
				$( "#" + attrs.id ).modal( 'show' );
			}
		}

		scope.deleteTransaction = function() {
	        if ( sessionStorage.transactionId > 0 ) {
	            TrxnService.delete( scope.transactionType, sessionStorage.transactionId ).done( function( response ) {
	                scope.errorMessages = [];
	                toastr.success( 'Successfully deleted' );
	                $( "#" + attrs.id ).modal( 'hide' );
	                setTimeout( function() {
	                	if ( scope.transactionType == 'expense' ) {
		                    $state.go( 'app.main.expense.main' );
		                } else {
		                    $state.go( 'app.main.sales.main' );
		                }
	                }, 1000 );
	            } );
	        }
	    }

		function initialize() {
			CommonFunc().initializeValidation( 'form.form-horizontal.form-delete-confirm', function( $form, errors ) {
				scope.$eval( attrs.deleteEvent ) ();
			} );
		}

		initialize();
	}

	return output;
}

angular
	.module( 'bookkeeping' )
	.directive( 'sejDeleteConfirmDialog', [ '$state', 'TrxnService', SejDeleteConfirmDialog ] );