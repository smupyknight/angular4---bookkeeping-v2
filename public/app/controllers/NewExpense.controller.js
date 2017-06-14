'use strict';

function NewExpenseCtrl( $scope, $state, MiscService, TrxnService ) {

	$scope.retrieve = function() {
		TrxnService.retrieve( 'expense', sessionStorage.transactionId ).done( function ( response ) {
            $scope.transaction = response.data.content;

            for ( var i = 0; i < $scope.transaction.expenseItems.length; i++ ) {
                $scope.transaction.expenseItems[i].removeIndex = i;
            }

            for ( var i = 0; i < $scope.transaction.expenseAccounts.length; i++ ) {
                $scope.transaction.expenseAccounts[i].removeIndex = i;
            }
            sessionStorage.preset = 0;
        });
	}

	$scope.setPayeeType = function( list, type ) {
		for ( var i = 0; i < list.length; i++ ) 
			list[i].type = type;
		return list;
	}

	$scope.calculateTotal = function() {
		var total = 0;
		var accounts = $scope.transaction.expenseAccounts;
		var items = $scope.transaction.expenseItems;

		for ( var i = 0; i < accounts.length; i++ ) {
			if ( accounts[i].rank != undefined && !isNaN( accounts[i].amount ) ) {
				total += parseFloat( accounts[i].amount );
			}
		}

		for ( var i = 0; i < items.length; i++ ) {
			if ( items[i].rank != undefined && !isNaN( items[i].amount ) ) {
				total += parseFloat( items[i].amount );
			}
		}

		$scope.transaction.transaction.total = total;
	}

	$scope.initialize = function() {
		TrxnFunc( $scope, $state, MiscService, TrxnService ).initialize();

		MiscService.massRetrieve( [ 'account', 'account_category_type', 'account_detail_type', 'product_category', 'product_service', 'customer', 'supplier' ] ).done( function( response ) {
			var content = response.data.content;

			$scope.accountNames = MiscService.extractNameList( content.account );
			$scope.accountUINames = MiscService.extractUniqueNameList( content.account );
			$scope.customerNames = $scope.setPayeeType( content.customer , 'Customer' );
			$scope.customerUINames = MiscService.extractUniqueNameList( content.customer );
			$scope.customerNames = $scope.customerNames.concat( $scope.setPayeeType( content.supplier, 'Supplier' ) );

		    $scope.accountCategoryTypeNames = MiscService.extractNameList( content.accountCategoryType );
            $scope.accountCategoryTypeUINames = MiscService.extractUniqueNameList( content.accountCategoryType );
            $scope.accountDetailTypeUINames = MiscService.extractUniqueNameList( content.accountDetailType );
            $scope.account.account_category_type = content.accountCategoryType[0].name;
            $scope.getAccountDetailTypeNames();

            $scope.productServices = content.productService;
            $scope.productServiceNames = MiscService.extractNameList( content.productService );
            $scope.productServiceUINames = MiscService.extractUniqueNameList( content.productService );
            $scope.productCategoryNames = MiscService.extractNameList( content.productCategory );
            $scope.newProductService.product_category = content.productCategory[0].name;
		} );

        if ( sessionStorage.transactionId > 0 ) {
        	$scope.retrieve();
        }
	}

	$scope.initialize();

}

angular
	.module( 'bookkeeping' )
	.controller( 'NewExpenseCtrl', [ '$scope', '$state', 'MiscService', 'TrxnService', NewExpenseCtrl ] );