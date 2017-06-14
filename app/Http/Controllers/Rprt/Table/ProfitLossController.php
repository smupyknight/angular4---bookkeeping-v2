<?php

namespace App\Http\Controllers\Rprt\Table;

use Illuminate\Http\Request;
use App\Http\Controllers\Base\BaseController;

use DB;
use App\Models\Sales;
use App\Models\Expense;
use App\Models\AccountCategoryType;
use App\Helper\DateFromToCalculator;
use App\Helper\RestResponseMessages;
class ProfitLossController extends BaseController
{
	public function getProfitLoss( $startDate, $endDate ) {
		$sales = Sales::where( 'transaction_type', 1 )
						->whereBetween( 'date', [ $startDate, $endDate ] )
						->sum( 'total' );

		$costOfSales = Expense::where( 'transaction_type', 1 )
						->whereIn( 'account_id', AccountCategoryType::find( 13 )->detailTypes()->pluck( 'id' ) )
						->whereBetween( 'date', [ $startDate, $endDate ] );

		$totalCostOfSales = $costOfSales->sum( 'total' );

		$costOfSales = $costOfSales->select( DB::raw( 'account_id as account_id' ), DB::raw( 'sum(total) as amount' ) )
						->groupBy( DB::raw( 'account_id' ) )
						->get();

		$grossProfit = $sales - $totalCostOfSales;

		$expenses = $costOfSales;
		$totalExpenses = $totalCostOfSales;

		$otherExpenses = Expense::where( 'transaction_type', 1 )
						->whereBetween( 'date', [ $startDate, $endDate ] )
						->whereNotIn( 'account_id', AccountCategoryType::find( 13 )->detailTypes()->pluck( 'id' ) );

		$totalOtherExpenses = $otherExpenses->sum( 'total' );
		$otherExpenses = $otherExpenses->select( DB::raw( 'account_id as account_id' ), DB::raw( 'sum(total) as amount' ) )
						->groupBy( DB::raw( 'account_id' ) )
						->get();

		$netEarnings = $grossProfit - $totalOtherExpenses;

		$total = $sales + $totalCostOfSales + $grossProfit + $totalExpenses + $totalOtherExpenses + $netEarnings;

		$content = [
			'sales' 				=> 		$sales,
			'totalIncome' 			=> 		$sales,
			'costOfSales' 			=> 		$costOfSales,
			'totalCostOfSales' 		=> 		$totalCostOfSales,
			'grossProfit' 			=> 		$grossProfit,
			'expenses' 				=> 		$expenses,
			'totalExpenses' 		=> 		$totalExpenses,
			'otherExpenses' 		=> 		$otherExpenses,
			'totalOtherExpenses' 	=> 		$totalOtherExpenses,
			'netEarnings' 			=> 		$netEarnings,
			'total'					=>		$total,
			'dateFrom'				=>		$startDate,
			'dateTo'				=>		$endDate
		];

		return $content;
	}

    public function period( $period ) {
    	$dateRange = DateFromToCalculator::calculateFromTo( $period );
    	$content = $this->getProfitLoss( $dateRange[ 'from' ], $dateRange[ 'to' ] );
		return RestResponseMessages::reportRetrieveSuccessMessage( 'Profit and Loss', $content );
    }

    public function range( $startDate, $endDate ) {
    	$content = $this->getProfitLoss( $startDate, $endDate );
    	return RestResponseMessages::reportRetrieveSuccessMessage( 'Profit and Loss', $content );
    }
}
