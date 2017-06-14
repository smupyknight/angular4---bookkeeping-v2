<?php

namespace App\Http\Controllers\TRXN;

use Illuminate\Http\Request;
use App\Http\Controllers\Base\BaseController;

use App\Helper\RestInputValidators;
use App\Helper\RestResponseMessages;
use App\Helper\StringConversionFunctions;

class TRXNController extends BaseController
{
	public $discountTypes 		= 	[ 	'Discount percent' 	=> 		1, 		'Discount value' 	=> 		2, 		'No discount' 		=> 		3 ];
    public $transactionTypes 	= 	[ 	'Invoice' 			=> 		1, 		'Payment' 			=> 		2, 		'Sales Receipt' 	=> 		3 ];
    public $statuses = 
    	[ 
            'Invoice' 			=> 	[ 	'Unpaid'			=> 		1, 		'Partial' 			=> 		2, 		'Paid' 				=> 		3 ],
            'Payment' 			=> 	[ 	'Unapplied' 		=> 		1, 		'Partial' 			=> 		2, 		'Closed' 			=> 		3 ],
            'Sales Receipt' 	=> 	[ 	'Paid' 				=> 		1 		]
        ];

    public function __construct() {
    	parent::__construct();

		$this->middleware( function( $request, $next ) {

			$validator = RestInputValidators::endPointIdValidator( $GLOBALS[ 'input' ] );

			if ( $validator->fails() ) {
				return RestResponseMessages::formValidationErrorMessage( $validator->errors()->all() );
			}

			$GLOBALS[ 'vltrName' ]     =   StringConversionFunctions::endPointIdToValidatorName( $GLOBALS[ 'input' ][ 'endPointId' ] );
			$GLOBALS[ 'validator' ]    =   RestInputValidators::$GLOBALS[ 'vltrName' ]( $GLOBALS[ 'input' ] );

			if ( $GLOBALS[ 'validator' ]->fails() ) {
				return RestResponseMessages::formValidationErrorMessage( $GLOBALS[ 'validator' ]->errors()->all() );
			}

			return $next( $request );
		});
    }
}
