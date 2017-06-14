<?php

namespace App\Http\Controllers\TRXN;
use App\Http\Controllers\Base\BaseController;

use Illuminate\Http\Request;

use App\Models\Sales;
use App\Models\Account;
use App\Models\Customer;
use App\Models\SalesReceipt;
use App\Models\SalesReceiptItem;

use App\Helper\RestInputValidators;
use App\Helper\RestResponseMessages;
use App\Helper\StringConversionFunctions;

class SalesReceiptController extends TRXNController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store( Request $request )
    {
        $sales = $GLOBALS[ 'input' ][ 'transaction' ];
        $salesReceipt = $GLOBALS[ 'input' ][ 'salesReceipt' ];
        $salesReceiptItems = array_filter( $GLOBALS[ 'input' ][ 'salesReceiptItems' ] );

        $sales[ 'customer_id' ] = $this->customer[ $sales[ 'customer' ] ]->id;
        $sales = Sales::create( $sales );

        $salesReceipt[ 'sales_id' ] = $sales->id;
        $salesReceipt = SalesReceipt::create( $salesReceipt );

        foreach ( $salesReceiptItems as $salesReceiptItem ) {
            if ( isset( $salesReceiptItem[ 'product_service' ] ) || $salesReceiptItem[ 'item_type' ] == 2 ) {
                $salesReceiptItem[ 'sales_receipt_id' ] = $salesReceipt->id;
                if ( $salesReceiptItem[ 'item_type' ] != 2 )
                    $salesReceiptItem[ 'product_service_id' ] = $this->product_service[ $salesReceiptItem[ 'product_service' ] ]->id;
                SalesReceiptItem::create( $salesReceiptItem );
            }
        }

        Customer::find( $sales[ 'customer_id' ] )->update( [ 'balance' => Customer::find( $sales[ 'customer_id' ] )->balance + $sales[ 'total' ] ] );
        
        $accountId = $this->account[ 'Cash' ]->id;
        Account::find( $accountId )->update( [ 'balance' => Account::find( $accountId )->balance + $sales[ 'total' ] ] );

        return RestResponseMessages::TRXNSuccessMessage( 'Create Sales Receipt', $sales, 200 );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show( $id )
    {
        $sales                  =   Sales::find( $id );
        $salesReceipt           =   SalesReceipt::where( 'sales_id', $id )->first();
        $salesReceiptItems      =   SalesReceiptItem::where( 'sales_receipt_id', $salesReceipt->id )->get();

        return RestResponseMessages::TRXNSuccessMessage('Get Sales Receipt', ['transaction' => $sales, 'salesReceipt' => $salesReceipt, 'salesReceiptItems' => $salesReceiptItems], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update( Request $request, $id )
    {
        $sales = $GLOBALS[ 'input' ][ 'transaction' ];
        $salesReceipt = $GLOBALS[ 'input' ][ 'salesReceipt' ];
        $salesReceiptItems = array_filter( $GLOBALS[ 'input' ][ 'salesReceiptItems' ] );

        $sales[ 'customer_id' ] = $this->customer[ $sales[ 'customer' ] ]->id;
        $orgSales = Sales::find( $sales[ 'id' ] );
        Sales::find( $sales[ 'id' ] )->update( $sales );

        $salesReceipt[ 'sales_id' ] = $sales[ 'id' ];
        SalesReceipt::find( $salesReceipt[ 'id' ] )->update( $salesReceipt );

        foreach ( $salesReceiptItems as $salesReceiptItem ) {
            if ( isset( $salesReceiptItem[ 'product_service' ] ) || $salesReceiptItem[ 'item_type' ] == 2 ) {
                $salesReceiptItem[ 'sales_receipt_id' ] = $salesReceipt[ 'id' ];
                if ( $salesReceiptItem[ 'item_type' ] != 2 )
                    $salesReceiptItem[ 'product_service_id' ] = $this->product_service[ $salesReceiptItem[ 'product_service' ] ]->id;

                if ( isset( $salesReceiptItem[ 'id' ] ) )
                    SalesReceiptItem::find( $salesReceiptItem[ 'id' ] )->update( $salesReceiptItem );
                else
                    SalesReceiptItem::create( $salesReceiptItem );
            }
        }

        Customer::find( $sales[ 'customer_id' ] )->update( [ 'balance' => Customer::find( $sales[ 'customer_id' ] )->balance + $sales[ 'total' ] - $orgSales[ 'total' ] ] );
        
        $accountId = $this->account[ 'Cash' ]->id;
        Account::find( $accountId )->update( [ 'balance' => Account::find( $accountId )->balance + $sales[ 'total' ] - $orgSales[ 'total' ] ] );

        return RestResponseMessages::TRXNSuccessMessage('Update Invoice', $sales, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy( Request $request, $id )
    {
        echo $id;
    }
}
