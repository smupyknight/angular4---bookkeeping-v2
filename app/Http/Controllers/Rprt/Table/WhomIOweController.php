<?php

namespace App\Http\Controllers\Rprt\Table;

use Illuminate\Http\Request;
use App\Http\Controllers\Base\BaseController;

use DB;
use App\Models\Expense;
use App\Helper\RestResponseMessages;

class WhomIOweController extends BaseController
{
    public function index() {
    	// $content = Expense::select( DB::raw('payee_id as payee_id'), DB::raw('sum(total) as amount'), DB::raw('payee_type as payee_type') )
    	// 					->groupBy(DB::raw('payee_id'))
    	// 					->get();

    	$content = [];
    	return RestResponseMessages::reportRetrieveSuccessMessage( 'Whom I Owe', $content );
    }
}
