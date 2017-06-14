<?php

namespace App\Http\Controllers\TRXN;

use Illuminate\Http\Request;
use App\Http\Controllers\Base\BaseController;

use DB;

use App\Models\Sales;
use App\Models\Account;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\MapInvoicePayment;

use App\Helper\RestResponseMessages;

class PaymentController extends TRXNController
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
        $payment = $GLOBALS[ 'input' ][ 'payment' ];

        $sales[ 'customer_id' ] = $this->customer[ $sales[ 'customer' ] ]->id;
        // if ( isset( $payment[ 'invoice_id' ] ) )
        //     $sales[ 'due_date' ] = Sales::find( $payment[ 'invoice_id' ] )->due_date;

        $invoices = $sales[ 'customerInvoices' ];
        $invoiceSum = 0;
        foreach ( $invoices as $invoice ) {
            if ( isset( $invoice[ 'checked' ] ) && $invoice[ 'checked' ] == true ) {
                $invoiceSum += $invoice[ 'amount' ];
                $status = $invoice[ 'balance' ] > $invoice[ 'amount' ] ?  'Partial' : 'Paid';
                Sales::find( $invoice[ 'id' ] )->update( [ 'status' => $this->statuses[ 'Invoice' ][ $status ], 'balance' => max( $invoice[ 'balance' ] - $invoice[ 'amount' ], 0 ) ] );
            }
        }
        $sales[ 'balance' ] = max( $sales[ 'total' ] - $invoiceSum, 0 );
        $status = $invoiceSum >= $sales[ 'total' ] ? 'Closed' : ( $invoiceSum == 0 ? 'Unapplied' : 'Partial' );
        $sales[ 'status' ] = $this->statuses[ 'Payment' ][ $status ];
        $sales = Sales::create($sales);

        $payment[ 'account_id' ] = $this->account[ $payment[ 'account' ] ]->id;
        $payment[ 'sales_id' ] = $sales->id;
        $payment = Payment::create( $payment );

        $mapInvoicePayment = [];
        $mapInvoicePayment[ 'payment_id' ] = $payment->id;
        foreach ( $invoices as $invoice ) {
            if ( isset( $invoice[ 'checked' ] ) && $invoice[ 'checked' ] == true ) {
                $mapInvoicePayment[ 'invoice_id' ] = Invoice::where( 'sales_id', $invoice[ 'id' ] )->first()->id;
                $mapInvoicePayment[ 'payment' ] = $invoice[ 'amount' ];
                if ( $invoice[ 'amount' ] > 0 )
                    MapInvoicePayment::create( $mapInvoicePayment );
            }
        }

        $accountId = $this->account[ 'Accounts Receivable' ]->id;
        Account::find( $accountId )->update( [ 'balance' => Account::find( $accountId )->balance - $sales[ 'total' ] ] );

        $accountId = $this->account[ 'Cash' ]->id;
        Account::find( $accountId )->update( [ 'balance' => Account::find( $accountId )->balance + $sales[ 'total' ] ] );

        return RestResponseMessages::TRXNSuccessMessage('Create Payment', $sales, 200);
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
        $payment        =   Payment::where( 'sales_id', $sales->id )->first();

        $sales[ 'customerInvoices' ] = MapInvoicePayment::where( 'payment_id', $payment->id )->join( 'invoice', 'map_invoice_payment.invoice_id', '=', 'invoice.id' )
                                            ->join( 'sales', 'sales.id', '=', 'invoice.sales_id' )->select( 'sales.id', 'sales.date', 'sales.invoice_receipt_no', 'sales.total', 'sales.balance', 'map_invoice_payment.payment', DB::raw( '1 as checked' ) )
                                            ->union( 
                                                Sales::where( 'transaction_type', $this->transactionTypes[ 'Invoice' ] )->where( 'customer_id', $sales->customer_id )->where( 'status', $this->statuses[ 'Invoice' ][ 'Unpaid' ] )
                                                        ->select( 'sales.id', 'sales.date', 'sales.invoice_receipt_no', 'sales.total', 'sales.balance', DB::raw( '0 as payment' ), DB::raw( '0 as checked' ) )
                                            )->get();

        $sales->customer = Customer::find( $sales->customer_id )->name;
        $payment->account = Account::find( $payment->account_id )->name;

        return RestResponseMessages::TRXNSuccessMessage( 'Get Payment', [ 'transaction' => $sales, 'payment' => $payment/*, 'invoices' => $invoices*/ ], 200 );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit( $id )
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
        $payment = $GLOBALS[ 'input' ][ 'payment' ];

        $sales[ 'customer_id' ] = $this->customer[ $sales[ 'customer' ] ]->id;
        // if ( isset( $payment[ 'invoice_id' ] ) )
        //     $sales[ 'due_date' ] = Sales::find( $payment[ 'invoice_id' ] )->due_date;
        
        $invoices = $sales[ 'customerInvoices' ];
        foreach ( $invoices as $invoice ) {
            $invoiceId = Invoice::where( 'sales_id', $invoice[ 'id' ] )->first()->id;
            $status = $invoice[ 'balance' ] == Sales::find( $invoice[ 'id' ] )->total ? 'Unpaid' : 'Partial';
            Sales::where( 'invoice_receipt_no', $invoice[ 'invoice_receipt_no' ] )->update( [ 'balance' => $invoice[ 'balance' ], 'status' => $this->statuses[ 'Invoice' ][ $status ] ] );
        }

        $invoices = $sales[ 'customerInvoices' ];
        $invoiceSum = 0;
        foreach ( $invoices as $invoice ) {
            if ( isset( $invoice[ 'checked' ] ) && $invoice[ 'checked' ] == true ) {
                $invoiceSum += $invoice[ 'amount' ];
                $status = $invoice[ 'balance' ] <=  $invoice[ 'amount' ] ? 'Paid' : ( $invoice [ 'amount' ] == 0 ? 'Unpaid' : 'Partial' );
                Sales::find( $invoice[ 'id' ] )->update( [ 'status' => $this->statuses[ 'Invoice' ][ $status ], 'balance' => max( $invoice[ 'balance' ] - $invoice[ 'amount' ], 0 ) ] );
            }
        }

        $sales[ 'balance' ] = max( $sales[ 'total' ] - $invoiceSum, 0 );
        $status = $invoiceSum >= $sales[ 'total' ] ? 'Closed' : ( $invoiceSum == 0 ? 'Unapplied' : 'Partial' );
        $sales[ 'status' ] = $this->statuses[ 'Payment' ][ $status ];
        $orgSales = Sales::find( $sales[ 'id' ] );
        Sales::find( $sales[ 'id' ] )->update( $sales );

        $payment[ 'account_id' ] = $this->account[ $payment[ 'account' ] ]->id;
        $payment[ 'sales_id' ] = $sales[ 'id' ];
        Payment::find( $payment[ 'id' ] )->update( $payment );

        $mapInvoicePayment = [];
        $mapInvoicePayment[ 'payment_id' ] = $payment[ 'id' ];
        foreach ( $invoices as $invoice ) {
            if ( isset( $invoice[ 'checked' ] ) && $invoice[ 'checked' ] == true ) {
                $mapInvoicePayment[ 'invoice_id' ] = Invoice::where( 'sales_id', $invoice[ 'id' ] )->first()->id;
                $mapInvoicePayment[ 'payment' ] = $invoice[ 'amount' ];

                $orgMap = MapInvoicePayment::where( 'invoice_id', $mapInvoicePayment[ 'invoice_id' ] )->where( 'payment_id', $payment[ 'id' ] )->first();
                if ( $orgMap ) {
                    if ( (int)$invoice [ 'amount' ] > 0 )
                        $orgMap->update( $mapInvoicePayment );
                    else
                        MapInvoicePayment::find( $orgMap->id )->delete();
                }
                else
                    MapInvoicePayment::create( $mapInvoicePayment );
            }
        }

        $accountId = $this->account[ 'Accounts Receivable' ]->id;
        Account::find( $accountId )->update( [ 'balance' => Account::find( $accountId )->balance - $sales[ 'total' ] + $orgSales[ 'total' ] ] );

        $accountId = $this->account[ 'Cash' ]->id;
        Account::find( $accountId )->update( [ 'balance' => Account::find( $accountId )->balance + $sales[ 'total' ] - $orgSales[ 'total' ] ] );

        return RestResponseMessages::TRXNSuccessMessage('Update Payment', $sales, 200);
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
