<?php

namespace App\Http\Controllers\Rprt\Chart;

use Illuminate\Http\Request;
use App\Http\Controllers\Base\BaseController;

use App\Models\Sales;
use App\Models\Expense;
use App\Helper\RestResponseMessages;
use App\Helper\DateFromToCalculator;

class ProfitLossController extends BaseController
{
    public function index( $period ) {

        $content            =   [];
    	$date_segments      =   DateFromToCalculator::calculateFromToSegments( $period );

    	foreach ( $date_segments as $date_segment ) {
    		$paid_invoice = Sales::where( 'transaction_type', 2 )
    							->where( 'status', 2 )
    							->whereBetween( 'date', [ $date_segment[ 'from' ], $date_segment[ 'to' ] ] )
    							->sum( 'total' );

    		$expense = Expense::where( 'transaction_type', 3 )
    							->whereBetween( 'date', [ $date_segment[ 'from' ], $date_segment[ 'to' ] ] )
    							->sum( 'total' );

    		array_push( $content, 
    			[
    				'display'   =>      $date_segment[ 'display' ],
    				'profit'    =>      $paid_invoice,
                    'loss'      =>      $expense
    			]
    		);
    	}

    	return RestResponseMessages::reportRetrieveSuccessMessage( 'Profit and Loss Report', $content );
    }
}
