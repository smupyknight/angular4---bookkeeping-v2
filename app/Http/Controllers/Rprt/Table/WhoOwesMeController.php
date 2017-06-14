<?php

namespace App\Http\Controllers\Rprt\Table;

use Illuminate\Http\Request;
use App\Http\Controllers\Base\BaseController;

use DB;
use App\Models\Sales;
use App\Helper\RestResponseMessages;

class WhoOwesMeController extends BaseController
{
    public function index() {
    	$content = Sales::where( 'transaction_type', 1 )
    					->where( 'status', 1 )
    					->select( DB::raw( 'customer_id as payee_id' ), DB::raw( 'sum(total) as amount' ) )
    					->groupBy( DB::raw( 'payee_id' ) )
    					->get();

    	return RestResponseMessages::reportRetrieveSuccessMessage( 'Who Owes Me', $content );
    }
}
