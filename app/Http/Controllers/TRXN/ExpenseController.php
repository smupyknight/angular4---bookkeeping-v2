<?php

namespace App\Http\Controllers\TRXN;

use Illuminate\Http\Request;
use App\Http\Controllers\Base\BaseController;

use App\Models\Account;
use App\Models\Expense;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\ExpenseItem;
use App\Models\ExpenseAccount;
use App\Models\ProductService;

use App\Helper\RestResponseMessages;

class ExpenseController extends TRXNController
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
        $additionalTables = [ 'expense_account', 'expense_item', 'attachment' ];

        $expense            = $GLOBALS[ 'input' ][ 'transaction' ];
        $expenseItems       = $GLOBALS[ 'input' ][ 'expenseItems' ];
        $expenseAccount     = $GLOBALS[ 'input' ][ 'expenseAccounts' ];

        $expense[ 'payee_id' ] = $this->payee[ $expense[ 'customer' ] ]->id;
        $expense[ 'payee_type' ] = $this->payee[ $expense[ 'customer' ] ]->type;

        $expense = Expense::create( $expense );

        foreach ( $expenseItems as $expenseItem ) {
            if ( isset( $expenseItem[ 'product_service' ] ) ) {
                $expenseItem[ 'expense_id' ] = $expense->id;
                $expenseItem[ 'product_service_id' ] = $this->product_service[ $expenseItem[ 'product_service' ] ]->id;
                ExpenseItem::create( $expenseItem );
            }
        }

        foreach ( $expenseAccount as $expenseAccount ) {
            if ( isset( $expenseAccount[ 'account' ] ) ) {
                $expenseAccount[ 'expense_id' ] = $expense->id;
                $expenseAccount[ 'account_id' ] = $this->account[ $expenseAccount[ 'account' ] ]->id;
                ExpenseAccount::create( $expenseAccount );
            }   
        }

        return RestResponseMessages::MiscSuccessMessage( 'Create Expense', $expense, 201 );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show( $id )
    {
        $expense            =   Expense::find( $id );
        $expenseItems       =   ExpenseItem::where( 'expense_id', $expense->id )->get();
        $expenseAccounts    =   ExpenseAccount::where( 'expense_id', $expense->id )->get();

        if ( $expense->payee_type == 1 ) {
            $expense->customer = Customer::find( $expense->payee_id )->name;
        } else if ( $expense->payee_type == 2 ) {
            $expense->customer = Supplier::find( $expense->payee_id )->name;
        }

        foreach ( $expenseItems as $expenseItem ) {
            $expenseItem->product_service = ProductService::find( $expenseItem->product_service_id )->name;
        }

        foreach ( $expenseAccounts as $expenseAccount ) {
            $expenseAccount->account = Account::find( $expenseAccount->account_id )->name;
        }

        return RestResponseMessages::TRXNSuccessMessage( 'Expense Retrieval', [ 'transaction' => $expense, 'expenseItems' => $expenseItems, 'expenseAccounts' => $expenseAccounts ], 200 );
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
        $expense = $GLOBALS[ 'input' ][ 'transaction' ];
        $expenseItems = $GLOBALS[ 'input' ][ 'expenseItems' ];
        $expenseAccount = $GLOBALS[ 'input' ][ 'expenseAccounts' ];

        $expense[ 'payee_id' ] = $this->payee[ $expense[ 'customer' ] ]->id;
        $expense[ 'payee_type' ] = $this->payee[ $expense[ 'customer' ] ]->type;

        Expense::find( $expense[ 'id' ] )->update( $expense );

        ExpenseItem::where( 'expense_id', $expense[ 'id' ] )->delete();
        ExpenseAccount::where( 'expense_id', $expense[ 'id' ] )->delete();

        foreach ( $expenseItems as $expenseItem ) {
            if ( isset( $expenseItem[ 'product_service' ] ) ) {
                $expenseItem[ 'expense_id' ] = $expense[ 'id' ];
                $expenseItem[ 'product_service_id' ] = $this->product_service[ $expenseItem[ 'product_service' ] ]->id;
                ExpenseItem::create( $expenseItem );
            }
        }

        foreach ( $expenseAccount as $expenseAccount ) {
            if ( isset( $expenseAccount[ 'account' ] ) ) {
                $expenseAccount[ 'expense_id' ] = $expense[ 'id' ];
                $expenseAccount[ 'account_id' ] = $this->account[ $expenseAccount[ 'account' ] ]->id;
                ExpenseAccount::create( $expenseAccount );
            }
        }

        return RestResponseMessages::MiscSuccessMessage( 'Update Expense', $expense, 201 );
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
