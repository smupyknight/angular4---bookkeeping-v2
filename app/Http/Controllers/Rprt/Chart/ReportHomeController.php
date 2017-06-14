<?php

namespace App\Http\Controllers\Rprt\Chart;

use Illuminate\Http\Request;
use App\Http\Controllers\Base\BaseController;

use App\Models\Sales;
use App\Models\Expense;
use App\Helper\RestResponseMessages;
use App\Helper\DateFromToCalculator;

class ReportHomeController extends BaseController
{
    public function index() {

    	$content 			= 	[];
    	$profit_loss 		= 	[];
    	$date_range 		=	DateFromToCalculator::calculateFromTo( 'Last 4 months' );
    	$date_segments 		= 	DateFromToCalculator::calculateFromToSegments( 'Last 4 months' );

    	$income_total = Sales::where( 'type', 1 )
    					->whereBetween( 'date', [ $date_range[ 'from' ], $date_range[ 'to' ] ] )
    					->sum( 'total' );

    	$expense_total = Expense::where( 'type', 1 )
    					->whereBetween( 'date', [ $date_range[ 'from' ], $date_range[ 'to' ] ] )
    					->sum( 'total' );

    	foreach ( $date_segments as $date_segment ) {

    		$paid_invoice = Sales::where( 'type', 2 )
    					->where( 'status', 2 )
    					->whereBetween( 'date', [ $date_segment[ 'from' ], $date_segment[ 'to' ] ] )
    					->sum( 'total' );

    		$expense = Expense::where( 'type', 1 )
    					->whereBetween( 'date', [ $date_segment[ 'from' ], $date_segment[ 'to' ] ] )
    					->sum( 'total' );

    		array_push( $profit_loss, 
    			[
    				'profit' 	=> 		$paid_invoice,
    				'loss' 		=> 		$expense
    			]
    		);
    		
    	}

    	$content = array(
    		'net_income' 	=> 		$income_total - $expense_total,
    		'income' 		=> 		$income_total,
    		'expense' 		=> 		$expense_total,
    		'profit_loss' 	=> 		$profit_loss
    	);

    	return RestResponseMessages::reportRetrieveSuccessMessage( 'Report Home', $content );
    }
}
