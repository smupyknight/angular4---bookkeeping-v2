<?php

namespace App\Http\Controllers\Misc;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Base\BaseController;

use DB;
use App;
use App\User;
use App\Models\Sales;
use App\Models\Account;
use App\Models\Invoice;
use App\Models\UserProfile;
use App\Models\AccountDetailType;

use App\Helper\RestInputValidators;
use App\Helper\RestResponseMessages;
use App\Helper\StringConversionFunctions;

class MiscController extends BaseController
{
	public function __construct( Request $request ) {
		parent::__construct();

		$this->middleware( function( $request, $next ) {

			$validator = RestInputValidators::endPointIdValidator( $GLOBALS[ 'input' ] );

			if ( $validator->fails() ) {
				return RestResponseMessages::formValidationErrorMessage( $validator->errors()->all() );
			}

			$GLOBALS[ 'vltrName' ]     =   StringConversionFunctions::endPointIdToValidatorName( $GLOBALS[ 'input' ][ 'endPointId' ] );
			$GLOBALS[ 'validator' ]    =   RestInputValidators::$GLOBALS[ 'vltrName' ]( $GLOBALS[ 'input' ] );

			if ( $GLOBALS[ 'validator' ]->fails() ) {
				return RestResponseMessages::formValidationErrorMessage( $GLOBALS[ 'validator' ]->errors()->all() );
			}

			return $next( $request );
		} );
	}

	public function getInvoiceById( Request $request ) {
        $invoice = Sales::where( 'invoice_receipt_no', $request->input( 'invoiceId' ) )->first();
        $customerId = $invoice->customer_id;
        $customerInvoices = Sales::where( 'transaction_type', 1 )->where( 'customer_id', $customerId )->where( 'status', '!=', '3' )->get();

        foreach ( $customerInvoices as $customerInvoice ) {
            if ( $customerInvoice->invoice_receipt_no == $request->input( 'invoiceId' ) ) {
                $customerInvoice->checked = true;
                $customerInvoice->amount = $customerInvoice->total;
                $invoice->amount_received = $customerInvoice->total;
            }
        }
        $invoice->customerInvoices = $customerInvoices;
		return RestResponseMessages::MiscSuccessMessage( 'Get Invoice By Id', $invoice, 200 );
	}

    public function getInvoiceNumber( Request $request ) {
        if ( $request->input( 'salesId' ) ) {
            return RestResponseMessages::MiscSuccessMessage( 'Invoice Id Retrieve', Sales::find( $request->input( 'salesId' ) )->invoice_receipt_no, 200 );
        } else {
            return RestResponseMessages::MiscSuccessMessage( 'Invoice Id Retrieve', Sales::max( 'invoice_receipt_no' ) + 1, 200 );
        }
    }

	public function getUserName( Request $request ) {
		return RestResponseMessages::MiscSuccessMessage( 'Get User Name', \Auth::user()->name, 200 );
	}

    public function massRetrieve( Request $request ) {

        $tableNames = json_decode( $request->input( 'tableNames' ) );
        $result = [];

        foreach( $tableNames as $tableName ) {
            $result = array_merge( $result, [ lcfirst( implode( '', array_map( 'ucfirst', explode( '_', $tableName ) ) ) ) => DB::table( $tableName )->get() ] );
        }

        return RestResponseMessages::MiscSuccessMessage( 'Mass Retrieve', $result, 200 );
    }

    public function massUpdate( Request $request ) {

        foreach ( $request->input( 'data' ) as $record ) {
            Account::find( $record[ 'id' ] )->update( $record );
        }

        return RestResponseMessages::MiscSuccessMessage( 'Account Mass Update', Account::all(), 200 );
    }

	public function retrieveAccountDetailTypeNames( Request $request ) {
		$accountCategoryTypeId = $this->account_category_type[ $request->input( 'accountCategoryType' ) ]->id;
		return RestResponseMessages::MiscSuccessMessage( 'Account Detail Type Retrieval', AccountDetailType::where( 'account_category_type_id', $accountCategoryTypeId )->pluck( 'name' ), 200 );
	}

    public function retrieveCustomerInvoices( Request $request ) {
        $customerName = $request->input( 'customerName' );
        $result = Sales::where( 'transaction_type', 1 )->where( 'customer_id', $this->customer[ $customerName ]->id )->where( 'status', '!=', '3' )->get();
        return RestResponseMessages::MiscSuccessMessage( 'Customer Invoices Retrieve', $result, 200 );
    }

    public function setUserProfile( Request $request ) {

    	$bPasswordReset = false;

    	$input = $request->all();
    	UserProfile::find( 1 )->update( $input );

    	if ( isset( $input[ 'new_password' ] ) ) {
    		if ( !Hash::check( $input[ 'current_password' ], UserProfile::find( 1 )->password ) )
    			return RestResponseMessages::formValidationErrorMessage( [ 'Current password incorrect' ] );

    		DB::table( 'user_profile' )->where( 'id', 1 )->update( [ 'password' => Hash::make( $input[ 'new_password' ] ) ] );
    		$input[ 'password' ] = Hash::make( $input[ 'new_password' ] );
    		$bPasswordReset = true;
        }

    	configureDBConnectionByName( env( 'DB_CONNECTION', 'mysql' ) );
        App::make( 'config' )->set( 'database.default', env( 'DB_CONNECTION', 'mysql' ) );

        User::find( \Auth::user()->id )->update( [ 'name' => $input[ 'name' ], 'email' => $input[ 'email' ] ] );

        if ( $bPasswordReset )
        	User::find( \Auth::user()->id )->update( [ 'password' => $input[ 'password' ] ] );

        configureDBConnectionByName( $this->dbName );
        App::make( 'config' )->set( 'database.default', $this->dbName );

    	return RestResponseMessages::MiscSuccessMessage( 'Set User Profile', $input, 200 );
    }
}
