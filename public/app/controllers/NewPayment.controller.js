'use strict';

function NewPaymentCtrl( $scope, $state, MiscService, TrxnService ) {

	$scope.accountNames = [];
	$scope.accountUINames = [];
	$scope.customerNames = [];
	$scope.customerUINames = [];
    $scope.customerInvoices = [];

    $scope.checkInvoice = function( invoice ) {
        if ( invoice.checked == 1 )
            invoice.amount = invoice.balance;
        else
            invoice.amount = 0;

        $scope.calculateAmountReceived();
    }

    $scope.checkAllInvoices = function() {

        var invoiceCount = $scope.transaction.transaction.customerInvoices.length;
        for ( var i = 0; i < invoiceCount; i++ ) {
            var invoice = $scope.transaction.transaction.customerInvoices[i];
            invoice.checked = $scope.transaction.allChecked;

            if ( $scope.transaction.allChecked == true )
                invoice.amount = invoice.balance;
            else
                invoice.amount = 0;
        }
        
        $scope.calculateAmountReceived();
    }

    $scope.setAmount = function( invoice ) {
        if ( invoice.amount > invoice.balance )
            invoice.amount = invoice.balance;

        $scope.calculateAmountReceived();
    }

    $scope.calculateAmountReceived = function() {
        $scope.transaction.transaction.total = 0;

        for ( var i = 0; i < $scope.transaction.transaction.customerInvoices.length; i++ ) {
            var customerInvoice = $scope.transaction.transaction.customerInvoices[i];
            if ( customerInvoice.checked == true ) {
                $scope.transaction.transaction.total += customerInvoice.amount;
            }
        }

    }

    $scope.retrieve = function() {
        TrxnService.retrieve( 'payment', sessionStorage.transactionId ).done( function ( response ) {
            $scope.transaction = response.data.content;
            var customerInvoices = $scope.transaction.transaction.customerInvoices;
            for ( var i = 0; i < customerInvoices.length; i++ ) {
                customerInvoices[i].amount = customerInvoices[i].payment;
                if ( !isNaN( customerInvoices[i].amount ) )
                    customerInvoices[i].balance += customerInvoices[i].amount;
                if ( customerInvoices[i].checked == '1' )
                    customerInvoices[i].checked = true;
            }
            sessionStorage.preset = 0;
        } );
    }

    $scope.retrieveCustomerInvoices = function() {
        MiscService.retrieveCustomerInvoices( $scope.transaction.transaction.customer ).done( function( response ) {
            $scope.transaction.transaction.customerInvoices = response.data.content;
        } );
    }

	$scope.searchByInvoiceId = function() {
        MiscService.getInvoiceById( $scope.transaction.payment.invoice_id ).done( function( response ) {
            $scope.transaction.transaction = response.data.content;
            $scope.transaction.transaction.customer = $scope.customerUINames[ $scope.transaction.transaction.customer_id ];
            $scope.transaction.payment.account = 'Accounts Receivable';
            $scope.errorMessages = [];
        } ).fail( function( response ) {
            $scope.errorMessages = response.data.content;
        } );
    }

	$scope.initialize = function() {
		TrxnFunc( $scope, $state, MiscService, TrxnService ).initialize();
        $scope.transaction.payment.account = 'Cash';

        if ( $scope.transaction.transaction.customer ) {
            $scope.retrieveCustomerInvoices();
        }

		MiscService.massRetrieve( [ 'account', 'account_category_type', 'account_detail_type', 'customer' ] ).done( function( response ) {
			var content = response.data.content;

			$scope.accountNames = MiscService.extractNameList( content.account );
			$scope.accountUINames = MiscService.extractUniqueNameList( content.account );
			$scope.customerNames = MiscService.extractNameList( content.customer );
			$scope.customerUINames = MiscService.extractUniqueNameList( content.customer );

		    $scope.accountCategoryTypeNames = MiscService.extractNameList( content.accountCategoryType );
            $scope.accountCategoryTypeUINames = MiscService.extractUniqueNameList( content.accountCategoryType );
            $scope.accountDetailTypeUINames = MiscService.extractUniqueNameList( content.accountDetailType );
            $scope.account.account_category_type = content.accountCategoryType[0].name;
            $scope.getAccountDetailTypeNames();

            if ( sessionStorage.invoiceId && sessionStorage.invoiceId != 'null' ) {
                $scope.transaction.payment.invoice_id = sessionStorage.invoiceId;
                $scope.searchByInvoiceId();
                sessionStorage.invoiceId = null;
            }
		} );

        if ( sessionStorage.transactionId > 0) {
        	$scope.retrieve();
        }
	}

	$scope.initialize();
}

angular
	.module( 'bookkeeping' )
	.controller( 'NewPaymentCtrl', [ '$scope', '$state', 'MiscService', 'TrxnService', NewPaymentCtrl ] );