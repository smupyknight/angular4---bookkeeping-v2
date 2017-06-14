<?php

namespace App\Http\Controllers\Rprt\Chart;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Base\BaseController;

use App\Helper\RestResponseMessages;
use App\Helper\DateFromToCalculator;
use App\Models\Expense;
use App\Models\ExpenseItem;

class ExpenseController extends BaseController
{
    public function dashboard( $period ) {

    	// Period : 30-days, this month, this quarter, this year, last month, last quarter, last year.

    	$date_range = DateFromToCalculator::calculateFromTo( $period );
    	$content = DB::table( 'expense_account' )
                        ->join( 'expense', 'expense.id', '=', 'expense_account.expense_id' )
    					->whereBetween( 'date', [ $date_range[ 'from' ], $date_range[ 'to' ] ] )
     					->select( DB::raw( 'expense_account.account_id as category_id' ), DB::raw( 'sum(total) as expense' ) )
					    ->groupBy( DB::raw( 'expense_account.account_id' ) )
					    ->get();

    	return RestResponseMessages::reportRetrieveSuccessMessage( 'Expense Report', $content );
    }

    public function companySnapshot( $period ) {
        $date_range = DateFromToCalculator::calculateFromTo( $period );
        $content = DB::table( 'expense_item' )
                        ->join( 'expense', function( $join ) {
                            $join->where( 'transaction_type', '3' )->on( 'expense.id', '=', 'expense_item.expense_id' );
                        } )
                        ->whereBetween( 'date', [ $date_range[ 'from' ], $date_range[ 'to' ] ] )
                        ->select( DB::raw( 'product_service_id as product_service_id' ), DB::raw( 'sum(amount) as expense' ) )
                        ->groupBy( DB::raw( 'product_service_id' ) )
                        ->get();

        return RestResponseMessages::reportRetrieveSuccessMessage( 'Expense Report', $content );
    }
}
