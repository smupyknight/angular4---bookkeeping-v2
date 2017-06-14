'use strict';

function NewInvoiceCtrl( $scope, $state, MiscService, TrxnService ) {

	$scope.transaction 	=	{};

	$scope.customerNames 			= 	[];
    $scope.customerUINames			=	[];
    $scope.productServices          =   [];
    $scope.productServiceNames 		= 	[];
    $scope.productServiceUINames	=	[];

	$scope.retrieve = function() {
		TrxnService.retrieve( 'invoice', sessionStorage.transactionId ).done( function ( response ) {
            $scope.transaction = response.data.content;
            $scope.transaction.transaction.customer = $scope.customerUINames[ $scope.transaction.transaction.customer_id ];

        	for ( var i = 0; i < $scope.transaction.invoiceItems.length; i++ ) {
                $scope.transaction.invoiceItems[i].removeIndex = i;
                $scope.transaction.invoiceItems[i].product_service = $scope.productServiceUINames[ $scope.transaction.invoiceItems[i].product_service_id ];
            }
            $scope.calculateDiscount();
            sessionStorage.preset = 0;
        } );
	}

    $scope.calculateDiscount = function() {
        var discountTypeIDs = { 'Discount percent' : 1, 'Discount value' : 2, 'No discount' : 3 };

        if ( $scope.transaction.invoice.discount_type_id == discountTypeIDs[ 'Discount percent' ] ) {
            $scope.discountValue = $scope.transaction.invoice.sub_total * $scope.transaction.invoice.discount_amount / 100;
        } else if ( $scope.transaction.invoice.discount_type_id == discountTypeIDs[ 'Discount value' ] ) {
            $scope.discountValue = $scope.transaction.invoice.discount_amount;
        } else {
            $scope.discountValue = 0;
        }
        $scope.calculateTotal();
    }

    $scope.calculateTotal = function() {
        var total = $scope.transaction.invoice.sub_total;
        
        if ( !isNaN( $scope.discountValue ) )
            total -= parseFloat( $scope.discountValue );
        if ( !isNaN( $scope.transaction.invoice.shipping ) )
            total += parseFloat( $scope.transaction.invoice.shipping );

        $scope.transaction.transaction.total = total;
        $scope.transaction.transaction.balance = total;
        $scope.transaction.balanceDue = $scope.transaction.invoice.deposit - $scope.transaction.transaction.total;
    }

	$scope.initialize = function() {
		TrxnFunc( $scope, $state, MiscService, TrxnService ).initialize();

		MiscService.massRetrieve( [ 'customer', 'product_service', 'product_category' ] ).done( function( response ) {
			var content = response.data.content;

			$scope.customerNames = MiscService.extractNameList( content.customer );
			$scope.customerUINames = MiscService.extractUniqueNameList( content.customer );
            $scope.productServices = content.productService;
			$scope.productServiceNames = MiscService.extractNameList( content.productService );
			$scope.productServiceUINames = MiscService.extractUniqueNameList( content.productService );
            $scope.productCategoryNames = MiscService.extractNameList( content.productCategory );

            if ( content.productCategory != undefined && content.productCategory.length )
                $scope.newProductService.product_category = content.productCategory[0].name;

            MiscService.getInvoiceNumber( sessionStorage.transactionId ).done( function( response ) {
                $scope.transaction.transaction.invoice_receipt_no = response.data.content;
            } );

            if ( sessionStorage.transactionId > 0 ) {
                $scope.retrieve();
            }
		} );
	}

	$scope.initialize();
}

angular
	.module( 'bookkeeping' )
	.controller( 'NewInvoiceCtrl', [ '$scope', '$state', 'MiscService', 'TrxnService', NewInvoiceCtrl ] )
