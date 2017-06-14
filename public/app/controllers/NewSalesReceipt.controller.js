'use strict';

function NewSalesReceiptCtrl( $scope, $state, MiscService, TrxnService ) {
	$scope.transaction 	=	{};

	$scope.customerNames 			= 	[];
    $scope.customerUINames			=	[];
    $scope.productServices          =   [];
    $scope.productServiceNames 		= 	[];
    $scope.productServiceUINames	=	[];

    $scope.retrieve = function() {
		TrxnService.retrieve( 'sales_receipt', sessionStorage.transactionId ).done( function ( response ) {
            var discountTypes = [ 'Discount percent', 'Discount value', 'No discount' ];

            $scope.transaction = response.data.content;
            $scope.transaction.transaction.customer = $scope.customerUINames[ $scope.transaction.transaction.customer_id ];
            $scope.transaction.salesReceipt.discount_type = discountTypes[ $scope.transaction.salesReceipt.discount_type_id - 1 ];

        	for ( var i = 0; i < $scope.transaction.salesReceiptItems.length; i++ ) {
                $scope.transaction.salesReceiptItems[i].removeIndex = i;
                $scope.transaction.salesReceiptItems[i].product_service = $scope.productServiceUINames[ $scope.transaction.salesReceiptItems[i].product_service_id ];
            }
            $scope.calculateDiscount();
            sessionStorage.preset = 0;
        });
	}

    $scope.calculateDiscount = function() {
        if ( $scope.transaction.salesReceipt.discount_type_id == 1 ) {
            $scope.discountValue = $scope.transaction.salesReceipt.sub_total * $scope.transaction.salesReceipt.discount_amount / 100;
        } else if ( $scope.transaction.salesReceipt.discount_type_id == 2 ) {
            $scope.discountValue = $scope.transaction.salesReceipt.discount_amount;
        } else {
            $scope.discountValue = 0;
        }
        $scope.calculateTotal();
    }

    $scope.calculateTotal = function() {
        var total = $scope.transaction.salesReceipt.sub_total;
        
        if ( !isNaN( $scope.discountValue ) )
            total -= parseFloat( $scope.discountValue );
        if ( !isNaN( $scope.transaction.salesReceipt.shipping ) )
            total += parseFloat( $scope.transaction.salesReceipt.shipping );

        $scope.transaction.transaction.total = total;
        $scope.transaction.balanceDue = $scope.transaction.salesReceipt.deposit - $scope.transaction.transaction.total;
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

            if ( sessionStorage.transactionId > 0) {
                $scope.retrieve();
            }
		} );
	}

	$scope.initialize();
}

angular
	.module( 'bookkeeping' )
	.controller( 'NewSalesReceiptCtrl', [ '$scope', '$state', 'MiscService', 'TrxnService', NewSalesReceiptCtrl ] );