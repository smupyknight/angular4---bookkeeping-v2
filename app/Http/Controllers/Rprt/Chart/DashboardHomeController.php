<?php

namespace App\Http\Controllers\Rprt\Chart;

use Illuminate\Http\Request;
use App\Http\Controllers\Base\BaseController;

use App\Models\Sales;
use App\Models\Expense;

use App\Helper\DateFromToCalculator;
use App\Helper\RestResponseMessages;

class DashboardHomeController extends BaseController
{
    public function index() {

    	$date_range = DateFromToCalculator::calculateFromTo( 'This year to date' );

    	$thisYearExpense = Expense::whereBetween( 'date', [ $date_range[ 'from' ], $date_range[ 'to' ] ] )
    						->sum( 'total' );
    	$thisYearIncome = Sales::whereBetween( 'date', [ $date_range[ 'from' ], $date_range[ 'to' ] ] )
    						->where( 'transaction_type', 1 )
    						->sum( 'total' );

    	return RestResponseMessages::reportRetrieveSuccessMessage( 'Dashboard Home', 
    		[
    			'thisYearExpense' => $thisYearExpense, 
    			'thisYearIncome' => $thisYearIncome, 
    			'thisYearProfitLoss' => $thisYearIncome - $thisYearExpense
    		], 
    	200 );
    }
}
