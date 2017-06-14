<?php

use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

function configureDBConnectionByName( $db_name ) {
	$config                         =   App::make( 'config' );
    $connections                    =   $config->get( 'database.connections' );
    $defaultConnection              =   $connections[ $config->get( 'database.default' ) ];
    $newConnection                  =   $defaultConnection;
    $newConnection[ 'database' ]      =   $db_name;

    App::make( 'config' )->set( 'database.connections.' . $db_name, $newConnection );
}

Route::middleware( 'auth:api' )->get( '/user', function ( Request $request ) {
    return $request->user();
} );

define( 'MISC',		'Misc\\' );
define( 'TRXN',		'TRXN\\' );
define( 'CHART', 	'Rprt\Chart\\' );
define( 'TABLE', 	'Rprt\Table\\' );

Route::group( [ 'middleware' => [ 'auth:api' ] ], function() {

	Route::group( [ 'prefix' => 'report/chart' ], function() {

		Route::get( 'dashboard_home',								CHART . 'DashboardHomeController@index' );
		Route::get( 'expense/dashboard/{period}',					CHART . 'ExpenseController@dashboard' );
		Route::get( 'expense/company_snapshot/{period}',			CHART . 'ExpenseController@companySnapshot' );
		Route::get( 'expense_comparison/{period}/{account}', 		CHART . 'ExpenseComparisonController@index' );
		Route::get( 'income_bar', 									CHART . 'IncomeController@barChart' );
		Route::get( 'income_circle/{period}',						CHART . 'IncomeController@circleChart' );
		Route::get( 'income_comparison/{period}/{account}', 		CHART . 'IncomeComparisonController@index' );
		Route::get( 'profit_loss/{period}',							CHART . 'ProfitLossController@index' );
		Route::get( 'report_home',									CHART . 'ReportHomeController@index' );
		Route::get( 'sales/{period}',								CHART . 'SalesController@index' );

	});

	Route::group( [ 'prefix' => 'report/table' ], function() {

		Route::get( 'balance_sheet/{period}',						TABLE . 'BalanceSheetController@period' );
		Route::get( 'balance_sheet/{start_date}/{end_date}',		TABLE . 'BalanceSheetController@range' );
		Route::get( 'expense_by_supplier/{period}',					TABLE . 'ExpenseBySupplierController@period' );
		Route::get( 'expense_by_supplier/{start_date}/{end_date}', 	TABLE . 'ExpenseBySupplierController@range' );
		Route::get( 'profit_loss/{period}',							TABLE . 'ProfitLossController@period' );
		Route::get( 'profit_loss/{start_date}/{end_date}',			TABLE . 'ProfitLossController@range' );
		Route::get( 'whom_i_owe', 									TABLE . 'WhomIOweController@index' );
		Route::get( 'who_owes_me', 									TABLE . 'WhoOwesMeController@index' );

	});

	Route::group( [ 'prefix' => 'misc' ], function() {

		Route::get( 'get_user_name',								MISC . 'MiscController@getUserName' );
		Route::get( 'get_invoice_by_id',							MISC . 'MiscController@getInvoiceById' );
		Route::get( 'get_invoice_number',							MISC . 'MiscController@getInvoiceNumber' );
		Route::get( 'mass_retrieve',								MISC . 'MiscController@massRetrieve' );
		Route::get( 'retrieve_account_detail_type_names',			MISC . 'MiscController@retrieveAccountDetailTypeNames' );
		Route::get( 'retrieve_customer_invoices',					MISC . 'MiscController@retrieveCustomerInvoices' );
 
		Route::put( 'mass_update',									MISC . 'MiscController@massUpdate' );
		Route::put( 'set_user_profile',								MISC . 'MiscController@setUserProfile' );

	});

	Route::group( [ 'prefix' => 'trxn' ], function() {

		Route::resource( '/expense',								'TRXN\ExpenseController');
		Route::resource( '/invoice',								'TRXN\InvoiceController');
		Route::resource( '/payment',								'TRXN\PaymentController');
		Route::resource( '/sales_receipt',							'TRXN\SalesReceiptController');

	});

	Route::resource( '/crud',										'CRUD\CRUDController' );

});

Route::post( '/signup', 'Auth\RegisterController@create' );
Route::post( '/signin', 'Auth\LoginController@login' );