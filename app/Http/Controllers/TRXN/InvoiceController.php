<?php

namespace App\Http\Controllers\TRXN;

use Illuminate\Http\Request;
use App\Http\Controllers\Base\BaseController;

use App\Models\Sales;
use App\Models\Account;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\InvoiceItem;
use App\Models\ProductService;
use App\Models\MapInvoicePayment;

use App\Helper\RestInputValidators;
use App\Helper\RestResponseMessages;
use App\Helper\StringConversionFunctions;

class InvoiceController extends TRXNController
{

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
        $sales          =   $GLOBALS[ 'input' ][ 'transaction' ];
        $invoice        =   $GLOBALS[ 'input' ][ 'invoice' ];
        $invoiceItems   =   array_filter( $GLOBALS[ 'input' ][ 'invoiceItems' ] );

        $sales[ 'customer_id' ] = $this->customer[ $sales[ 'customer' ] ]->id;
        $unClosedPayments = Sales::where( 'customer_id', $sales[ 'customer_id' ] )->where( 'transaction_type', $this->transactionTypes[ 'Payment' ] )->where( 'status', '!=', $this->statuses[ 'Payment' ][ 'Closed' ] );
        $unClosedTotal = $unClosedPayments->sum( 'balance' );
        if ( $unClosedTotal > 0 ) {
            $sales[ 'balance' ] = max( $sales[ 'total' ] - $unClosedTotal, 0 );
            $sales[ 'status' ] = $sales[ 'balance' ] > 0 ? $this->statuses[ 'Invoice' ][ 'Partial' ] : $this->statuses[ 'Invoice' ][ 'Paid' ];
        }
        $sales = Sales::create( $sales );

        $invoice[ 'sales_id' ] = $sales->id;
        $invoice = Invoice::create( $invoice );

        $unClosedPayments = $unClosedPayments->get();
        $mapInvoicePayment = [ 'invoice_id' => $invoice->id ];
        $total = $sales[ 'total' ];
        foreach ( $unClosedPayments as $payment ) {
            if ( $total > 0 ) {
                $status = $payment->balance > $total ? 'Partial' : 'Closed';
                Sales::find( $payment->id )->update( [ 'status' => $this->statuses[ 'Payment' ][ $status ], 'balance' => max( $payment->balance - $total, 0 ) ] );

                $mapInvoicePayment[ 'payment' ] = min( $payment->balance, $total );
                $mapInvoicePayment[ 'payment_id' ] = Payment::where( 'sales_id', $payment->id )->first()->id;
                MapInvoicePayment::create( $mapInvoicePayment );

                $total -= $payment->balance;
            }
        }

        foreach ( $invoiceItems as $invoiceItem ) {
            if ( isset( $invoiceItem[ 'product_service' ] ) || $invoiceItem[ 'item_type' ] == 2 ) {
                $invoiceItem[ 'invoice_id' ] = $invoice->id;
                if ( $invoiceItem[ 'item_type' ] != 2 )
                    $invoiceItem[ 'product_service_id' ] = $this->product_service[ $invoiceItem[ 'product_service' ] ]->id;
                InvoiceItem::create( $invoiceItem );
            }
        }

        Customer::find( $sales[ 'customer_id' ] )->update( [ 'balance' => Customer::find( $sales[ 'customer_id' ] )->balance + $sales[ 'total' ] ] );
        
        $accountId = $this->account[ 'Accounts Receivable' ]->id;
        Account::find( $accountId )->update( [ 'balance' => Account::find( $accountId )->balance + $sales[ 'total' ] ] );

        return RestResponseMessages::TRXNSuccessMessage( 'Create Invoice', $sales, 200 );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show( $id )
    {
        $sales          =   Sales::find( $id );
        $invoice        =   Invoice::where( 'sales_id', $id )->first();
        $invoiceItems   =   InvoiceItem::where( 'invoice_id', $invoice->id )->get();

        return RestResponseMessages::TRXNSuccessMessage( 'Get Invoice', [ 'transaction' => $sales, 'invoice' => $invoice, 'invoiceItems' => $invoiceItems ], 200 );
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
        $invoice = $GLOBALS[ 'input' ][ 'invoice' ];
        $invoiceItems = array_filter( $GLOBALS[ 'input' ][ 'invoiceItems' ] );

        $sales[ 'customer_id' ] = $this->customer[ $sales[ 'customer' ] ]->id;
        $orgPayments = Sales::where( 'transaction_type', $this->transactionTypes[ 'Payment' ] )->where( 'customer_id', $sales[ 'customer_id' ] )->get();
        foreach ( $orgPayments as $orgPayment ) {
            $orgBalance = MapInvoicePayment::where( 'invoice_id', $invoice[ 'id' ] )->where( 'payment_id', Payment::where( 'sales_id', $orgPayment->id )->first()->id )->first();
            if ( $orgBalance ) {
                $orgBalance = $orgPayment->balance + $orgBalance->payment;
                $status = $orgBalance == $orgPayment->total ? 'Unapplied' : ( $orgBalance == 0 ? 'Closed' : 'Partial' );
                $orgPayment->update( [ 'balance' => $orgBalance, 'status' => $this->statuses[ 'Payment' ][ $status ] ] );
            }
        }
        $unClosedTotal = Sales::where( 'customer_id', $sales[ 'customer_id' ] )->where( 'transaction_type', $this->transactionTypes[ 'Payment' ] )->where( 'status', '!=', $this->statuses[ 'Payment' ][ 'Closed' ] )->sum( 'balance' );
        if ( $unClosedTotal > 0 ) {
            $sales[ 'balance' ] = max( $sales[ 'total' ] - $unClosedTotal, 0 );
            $sales[ 'status' ] = $sales[ 'balance' ] > 0 ? $this->statuses[ 'Invoice' ][ 'Partial' ] : $this->statuses[ 'Invoice' ][ 'Paid' ];
        }
        $orgSales = Sales::find( $sales[ 'id' ] );
        Sales::find( $sales[ 'id' ] )->update( $sales );

        $invoice[ 'sales_id' ] = $sales[ 'id' ];
        Invoice::find( $invoice[ 'id' ] )->update( $invoice );

        $mapInvoicePayment = [];
        $mapInvoicePayment[ 'invoice_id' ] = $invoice[ 'id' ];
        $unClosedPayments = Sales::where( 'customer_id', $sales[ 'customer_id' ] )->where( 'transaction_type', $this->transactionTypes[ 'Payment' ] )->where( 'status', '!=', $this->statuses[ 'Payment' ][ 'Closed' ] )->get();

        if ( count( $unClosedPayments ) > 0 ) {
            $total = $sales[ 'total' ];
            foreach ( $unClosedPayments as $payment ) {
                if ( $total > 0 ) {
                    $status = $payment->balance > $total ? 'Partial' : 'Closed';
                    Sales::find( $payment->id )->update( [ 'status' => $this->statuses[ 'Payment' ][ $status ], 'balance' => max( $payment->balance - $total, 0 ) ] );

                    $mapInvoicePayment[ 'payment' ] = min( $payment->balance, $total );
                    $mapInvoicePayment[ 'payment_id' ] = Payment::where( 'sales_id', $payment->id )->first()->id;
                    $orgMap = MapInvoicePayment::where( 'invoice_id', $mapInvoicePayment[ 'invoice_id' ] )->where( 'payment_id', $mapInvoicePayment[ 'payment_id' ] )->first();
                    if ( $orgMap )
                        $orgMap->update( $mapInvoicePayment );
                    else
                        MapInvoicePayment::create( $mapInvoicePayment );

                    $total -= $payment->balance;
                }
            }
        }

        foreach ( $invoiceItems as $invoiceItem ) {
            if ( isset( $invoiceItem[ 'product_service' ] ) || $invoiceItem[ 'item_type' ] == 2 ) {
                $invoiceItem[ 'invoice_id' ] = $invoice[ 'id' ];
                if ( $invoiceItem[ 'item_type' ] != 2 )
                    $invoiceItem[ 'product_service_id' ] = $this->product_service[ $invoiceItem[ 'product_service' ] ]->id;

                if ( isset( $invoiceItem[ 'id' ] ) )
                    InvoiceItem::find( $invoiceItem[ 'id' ] )->update( $invoiceItem );
                else
                    InvoiceItem::create( $invoiceItem );
            }
        }

        Customer::find( $sales[ 'customer_id' ] )->update( [ 'balance' => Customer::find( $sales[ 'customer_id' ] )->balance + $sales[ 'total' ] - $orgSales->total ] );
        
        $accountId = $this->account[ 'Accounts Receivable' ]->id;
        Account::find( $accountId )->update( [ 'balance' => Account::find( $accountId )->balance + $sales[ 'total' ] - $orgSales->total ] );

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
        $sales = Sales::find( $id );

        Customer::find( $sales->customer_id )->update( [ 'balance' => Customer::find( $sales->customer_id )->balance - $sales->total ] );
        
        $accountId = $this->account[ 'Accounts Receivable' ]->id;
        Account::find( $accountId )->update( [ 'balance' => Account::find( $accountId )->balance - $sales->total ] );

        $sales->delete();
    }
}
