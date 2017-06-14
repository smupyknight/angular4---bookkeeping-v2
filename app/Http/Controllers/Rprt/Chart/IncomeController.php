<?php

namespace App\Http\Controllers\Rprt\Chart;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Base\BaseController;
use App\Models\Sales;
use App\Models\InvoiceItem;

use App\Helper\RestResponseMessages;
use App\Helper\DateFromToCalculator;

class IncomeController extends BaseController
{
    public function barChart() {
    	$open_invoices = Sales::where( 'transaction_type', 1 )
    							->where( 'status', 1 )
                                ->whereBetween( 'date', [ date( 'Y-m-d', strtotime( '-365 days' ) ), date( 'Y-m-d', strtotime( '+1 day' ) ) ] )
    							->sum( 'total' );

    	$over_due = Sales::where( 'transaction_type', 1 )
						    	->where( 'status', 1 )
                                ->whereBetween( 'date', [ date( 'Y-m-d', strtotime( '-365 days' ) ), date( 'Y-m-d', strtotime( '+1 day' ) ) ] )
						    	->where( 'due_date', '<', date( 'Y-m-d' ) )
						    	->sum( 'total' );

    	$paid_last_30_days = Sales::where( 'transaction_type', 2 )
							    ->where( 'status', 2 )
							    ->whereBetween( 'date', [ date( 'Y-m-d', strtotime( '-30 days' ) ), date( 'Y-m-d', strtotime( '+1 day' ) ) ] )
							    ->sum( 'total' );

    	$content = array( 'open_invoices' => $open_invoices, 'over_due' => $over_due, 'paid_last_30_days' => $paid_last_30_days );
    	return RestResponseMessages::reportRetrieveSuccessMessage( 'Income Report(Bar)', $content );
    }

    public function circleChart( $period ) {
        $date_range = DateFromToCalculator::calculateFromTo( $period );

        $content = InvoiceItem::join( 'sales', function( $join ) {
                                $join->where( 'sales.transaction_type', '=', '1' )->on( 'sales.transaction_id', '=', 'invoice_id' );
                            } )->whereBetween( 'date', [ $date_range[ 'from' ], $date_range[ 'to' ] ] )
                            ->where( 'product_service_id', '!=', '0' )
                            ->select( DB::raw( 'product_service_id as product_service_id' ), DB::raw( 'sum(amount) as income' ) )
                            ->groupBy( DB::raw( 'product_service_id' ) )
                            ->get();

        return RestResponseMessages::reportRetrieveSuccessMessage( 'Income Report(Chart)', $content );
    }
}
