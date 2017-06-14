<?php

namespace App\Http\Controllers\Rprt\Chart;

use Illuminate\Http\Request;
use App\Http\Controllers\Base\BaseController;

use App\Models\Expense;
use App\Models\Account;
use App\Helper\RestResponseMessages;
use App\Helper\DateFromToCalculator;

class ExpenseComparisonController extends BaseController
{
    public function index( $period, $account_type ) {
    	$date_segments = DateFromToCalculator::calculateFromToSegments( $period );
    	$content = [];

    	foreach ( $date_segments as $date_segment ) {
    		$this_year = Expense::whereBetween( 'date', [ $date_segment[ 'this_year' ][ 'from' ], $date_segment[ 'this_year' ][ 'to' ] ] );
    		$last_year = Expense::whereBetween( 'date', [ $date_segment[ 'last_year' ][ 'from' ], $date_segment[ 'last_year' ][ 'to' ] ] );

    		if ( $account_type != 'All accounts' ) {
    			$this_year = $this_year->where( 'category_id', $account[ $account_type ]->id );
    			$last_year = $last_year->where( 'category_id', $account[ $account_type ]->id );
    		}

    		$this_year = $this_year->sum( 'total' );
    		$last_year = $last_year->sum( 'total' );

    		array_push( $content, 
    			[
    				'this_year' => $this_year,
    				'last_year' => $last_year,
    				'display' => $date_segment[ 'Display' ]
    			]
    		);
    	}

    	return RestResponseMessages::reportRetrieveSuccessMessage( 'Expense Comparison', $content );
    }
}
