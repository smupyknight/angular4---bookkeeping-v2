<?php

namespace App\Http\Controllers\Rprt\Chart;

use Illuminate\Http\Request;
use App\Http\Controllers\Base\BaseController;
use App\Models\Sales;

use App\Helper\DateFromToCalculator;
use App\Helper\RestResponseMessages;

class SalesController extends BaseController
{
    public function index( $period ) {
    	
        $content            =   [];
    	$date_segments      =   DateFromToCalculator::calculateFromToSegments( $period );

    	foreach ( $date_segments as $date_range ) {
    		$total = Sales::where( 'transaction_type', 1 )
    					->whereBetween( 'date', [ $date_range[ 'from' ], $date_range[ 'to' ] ] )
    					->sum( 'total' );

    		array_push($content, 
    			[
    				'display'   =>      $date_range[ 'display' ],
    				'sales'     =>      $total
    			]
    		);
    	}

    	return RestResponseMessages::reportRetrieveSuccessMessage( 'Sales Report', $content );
    }
}
