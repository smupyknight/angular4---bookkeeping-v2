
function TrxnFunc( $scope, $state, MiscService, TrxnService ) {

	var output = {};
	
	$scope.newTransaction 			=	{ transaction: { status: 1 }, payment: { account: 'Cash' }, invoice: { discount_type_id: 3, deposit: 0, shipping: 0, discount_amount: 0 }, salesReceipt: { discount_type_id: 3, deposit: 0, shipping: 0, discount_amount: 0 }, expenseAccounts: [], expenseItems: [], invoiceItems: [], salesReceiptItems: [], balanceDue: 0 };
	$scope.newExpenseItem 			= 	{ rank: 1, product_service: '', description: '', qty: '', rate: '', amount: 0, removeIndex: 0 };
	$scope.newExpenseAccount 		= 	{ rank: 1, account: '', description: '', amount: 0, removeIndex: 0 };
	$scope.newInvoiceItem 			= 	{ rank: 1, item_type: 1, product_service: '', description: '', qty: '', rate: '', amount: '', removeIndex: 0 };	
    $scope.newSalesReceiptItem      =   { rank: 1, item_type: 1, product_service: '', description: '', qty: '', rate: '', amount: '', removeIndex: 0 }; 

	$scope.createOrUpdateSuccess = function( bAddNew, strCreatedOrUpdated ) {
		if ( bAddNew == true ) {
            if ( $scope.transactionType == 'invoice' || $scope.transactionType == 'sales_receipt' ) {
                MiscService.getInvoiceNumber( '' ).done( function( response ) {
                    sessionStorage.transactionId = 0;
                    $scope.transaction.transaction.invoice_receipt_no = response.data.content;
                    $scope.clearAll();
                } );
            } else {
                $scope.clearAll();
            }
        } else {
        	if ( $scope.transactionType == 'expense' )
        		$state.go( 'app.main.expense.main' );
        	else
        		$state.go( 'app.main.sales.main' );
        }
        $scope.errorMessages = [];
        toastr.success( 'Successfully ' + strCreatedOrUpdated );
	}

	$scope.createOrUpdate = function( bAddNew ) {
		if ( sessionStorage.transactionId > 0 ) {
			$scope.update( bAddNew );
		} else {
			$scope.create( bAddNew );
		}
	}

	$scope.create = function( bAddNew ) {
        $scope.transaction.endPointId = $scope.transactionType;
        TrxnService.create( $scope.transactionType, $scope.transaction ).done( function( response ) {
            $scope.createOrUpdateSuccess( bAddNew, 'created' );
        } ).fail( function( response ) {
            $scope.errorMessages = response.data.content;
        } );
    }

    $scope.update = function( bAddNew ) {
        $scope.transaction.endPointId = $scope.transactionType;
        TrxnService.update( $scope.transactionType, sessionStorage.transactionId, $scope.transaction ).done( function( response ) {
            $scope.preset = 0;
            $scope.createOrUpdateSuccess( bAddNew, 'updated' );
        } ).fail( function( response ) {
            $scope.errorMessages = response.data.content;
        } );
    }

    $scope.addLines = function( list, line ) {
        for ( var i = 0; i < 3; i++ ) {
            line.removeIndex = list.length;
            list.push( CommonFunc().cloneObject( line ) );
        }
        $scope.reArrangeRankNumbers( list );
    }

    $scope.addSubTotal = function( list ) {
        var subTotal = 0;
        for ( var i = list.length - 1; i >= 0; i-- ) {
            if ( list[i].item_type == 2 )
                break;
            if ( !isNaN( list[i].amount ) )
                subTotal += parseInt( list[i].amount );
        }

        list.push( { rank: list.length + 1 , item_type: 2, description: '', qty: '', rate: '', amount: subTotal, removeIndex: list.length } );
        $scope.reArrangeRankNumbers( list );
    }

    $scope.clearAllLines = function( list, line ) {
        list.splice( 0, list.length );
        $scope.addLines( list, line );
    }

    $scope.removeLine = function( list, index ) {
        list[ index ] = {};
        $scope.reArrangeRankNumbers( list );
        $scope.calculateAmount( undefined, list );
    }

    $scope.reArrangeRankNumbers = function( list ) {
        var rank = 1;
        for ( var i = 0; i < list.length; i++ ) {
            if ( typeof list[i].rank != 'undefined' ) {
                list[i].rank = rank;
                rank ++;
            }
        }
    }

    $scope.clearAll = function() {
        var transactionTypes = { 'invoice': 1, 'payment': 2, 'sales_receipt': 3, 'expense': 4 };

        if ( $scope.transaction && $scope.transaction.transaction )
            var invoice_receipt_no = $scope.transaction.transaction.invoice_receipt_no;

    	$scope.transaction =  CommonFunc().cloneObject( $scope.newTransaction );

    	$scope.clearAllLines( $scope.transaction.invoiceItems, 	$scope.newInvoiceItem );
    	$scope.clearAllLines( $scope.transaction.expenseItems, 	$scope.newExpenseItem );
    	$scope.clearAllLines( $scope.transaction.expenseAccounts, 	$scope.newExpenseAccount );
        $scope.clearAllLines( $scope.transaction.salesReceiptItems, $scope.newSalesReceiptItem );

        $scope.transaction.transaction.transaction_type = transactionTypes[ $scope.transactionType ];
        $scope.transaction.transaction.invoice_receipt_no = invoice_receipt_no;
        setTimeout( function() {
            $( '.dp-month-year' ).datepicker( 'setDate', new Date() );
            $( '.dp-month-year' ).trigger( 'change' );
        }, 100);
    }

    $scope.print = function() {
        window.print();
    }

    $scope.selectPersonType = function() {
        $('#selectPersonType').show();
    }

    $scope.setProductServiceRate = function( productService, productServices, invoiceItem ) {
        for ( var i = 0; i < productServices.length; i++ ) {
            if ( productServices[i].name == productService ) {
                invoiceItem.rate = productServices[i].price;
            }
        }
    }

    $scope.calculateAmount = function( item, items ) {
    	var subSum = 0;

        if ( $scope.transaction.invoice )
            $scope.transaction.invoice.sub_total = 0;
        if ( $scope.transaction.salesReceipt )
            $scope.transaction.salesReceipt.sub_total = 0;

    	if ( item ) {
    		item.amount = item.qty * item.rate;
    	}

    	if ( items ) {
    		for ( var i = 0; i < items.length; i++ ) {
    			if ( items[i].rank == 'undefined' )
    				continue;

	    		if ( items[i].item_type == 1 ) {
	    			if ( items[i].amount > 0 && !isNaN( items[i].amount ) ) {
                        if ( $scope.transaction.invoice )
                            $scope.transaction.invoice.sub_total += parseFloat( items[i].amount );
                        if ( $scope.transaction.salesReceipt )
                            $scope.transaction.salesReceipt.sub_total += parseFloat( items[i].amount );
		    			subSum += parseFloat( items[i].amount );
		    		}
	    		} else if ( items[i].item_type == 2 ) {
	    			items[i].amount = subSum;
	    			subSum = 0;
	    		}
	    	}
    	}

    	if ( typeof $scope.calculateTotal != 'undefined' ) {
    		$scope.calculateTotal();
    	}
    }

    $( 'body' ).on( 'keypress', 'input[type="number"]', function( event ) {
        var charCode = ( event.which ) ? event.which : event.keyCode;

        var FULLSTOP = 46;
        var LEFTARROW = 37;
        var RIGHTARROW = 39;

        if ( charCode > 31 && ( charCode < 48 || charCode > 57 ) && charCode != LEFTARROW && charCode != RIGHTARROW )
            if ( !$(this).hasClass('decimal') || charCode != FULLSTOP )
                return false;
        return true;
    } );

	output.initialize = function() {
        $scope.clearAll();
        CommonFunc().initializeDatePicker( '.dp-month-year', $scope );
        CommonFunc().initializeDropzone( '#dpz-single-file' );

        if ( sessionStorage.personName && sessionStorage.personName != 'null' ) {
        	$scope.transaction.transaction.customer = sessionStorage.personName;
        	sessionStorage.personName = null;
        }

        if (sessionStorage.preset != 1) {
            sessionStorage.transactionId = 0;
        }
	}

	return output;
}