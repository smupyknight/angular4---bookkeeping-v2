<?php

namespace App\Http\Controllers\Rprt\Table;

use Illuminate\Http\Request;
use App\Http\Controllers\Base\BaseController;

use App\Models\Sales;
use App\Models\Expense;
use App\Models\Invoice;
use App\Helper\RestResponseMessages;
use App\Helper\DateFromToCalculator;

class BalanceSheetController extends BaseController
{
    public function getBalanceSheet( $startDate, $endDate ) {
        $cash = Expense::where( 'transaction_type', 3 )
                        ->whereBetween( 'date', [ $startDate, $endDate ] )
                        ->sum( 'total' ) +
                Sales::where( 'transaction_type', 1 )
                        ->where( 'status', 2 )
                        ->whereBetween( 'date', [ $startDate, $endDate ] )
                        ->sum( 'total' );

        $netIncome = Sales::where( 'transaction_type', 1 )
                        ->whereBetween( 'date', [ $startDate, $endDate ] )
                        ->sum( 'total' ) -
                    Expense::where( 'transaction_type', 3 )
                        ->whereBetween( 'date', [ $startDate, $endDate ] )
                        ->sum( 'total' );

        $retainedEarnings = $cash - $netIncome;
        $totalShareholdersEquity = $netIncome + $retainedEarnings;

        $content = [
            'cash'                          =>      $cash,
            'total_current_assets'          =>      $cash,
            'total_assets'                  =>      $cash,
            'netIncome'                     =>      $netIncome,
            'retainedEarnings'              =>      $retainedEarnings,
            'totalShareholdersEquity'       =>      $totalShareholdersEquity,
            'totalLiabilitiesEquity'        =>      $totalShareholdersEquity,
            'dateFrom'                      =>      $startDate,
            'dateTo'                        =>      $endDate
        ];

        return $content;
    }

    public function period( $period ) {
    	$dateRange = DateFromToCalculator::calculateFromTo( $period );
        $content = $this->getBalanceSheet( $dateRange[ 'from' ], $dateRange[ 'to' ] );
    	return RestResponseMessages::reportRetrieveSuccessMessage( 'Balance Sheet', $content );
    }

    public function range( $startDate, $endDate ) {
        $content = $this->getBalanceSheet( $startDate, $endDate );
        return RestResponseMessages::reportRetrieveSuccessMessage( 'Balance Sheet', $content );
    }
}
