<?php

namespace App\Http\Controllers\CRUD;

use Illuminate\Http\Request;
use App\Http\Controllers\Base\BaseController;

use App\Models\Invoice;
use App\Models\Payment;
use App\Helper\RestInputValidators;
use App\Helper\RestResponseMessages;
use App\Helper\StringConversionFunctions;

class CRUDController extends BaseController
{
    public function __construct( Request $request ) {
        parent::__construct();

        $this->middleware( function( $request, $next ) {

            $validator = RestInputValidators::tableNameValidator( $GLOBALS[ 'input' ] );

            if ( $validator->fails() ) {
                return RestResponseMessages::formValidationErrorMessage( $validator->errors()->all() );
            }

            $GLOBALS[ 'tblName' ]    =   $GLOBALS[ 'input' ][ 'tableName' ];
            $GLOBALS[ 'mdlName' ]    =   StringConversionFunctions::tableNameToModelName( $GLOBALS[ 'tblName' ] );

            if ( $request->isMethod( 'post' ) ) {

                $GLOBALS[ 'vltrName' ]      =   StringConversionFunctions::tableNameToValidatorName( $GLOBALS[ 'tblName' ] );
                $GLOBALS[ 'validator' ]     =   RestInputValidators::$GLOBALS[ 'vltrName' ]( $GLOBALS[ 'input' ] );

                if ( $GLOBALS[ 'validator' ]->fails() ) {
                    return RestResponseMessages::formValidationErrorMessage( $GLOBALS[ 'validator' ]->errors()->all() );
                }
            }
            
            return $next( $request );
        } );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        return RestResponseMessages::CRUDSuccessMessage( 'Retrieve', $GLOBALS[ 'tblName' ], $GLOBALS[ 'mdlName' ]::get(), 200 );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store( Request $request )
    {
        return RestResponseMessages::CRUDSuccessMessage( 'Create', $GLOBALS[ 'tblName' ], $GLOBALS[ 'mdlName' ]::create( $GLOBALS[ 'input' ] ), 201 );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show( $id )
    {
        return RestResponseMessages::CRUDSuccessMessage( 'Retrieve One', $GLOBALS[ 'tblName' ], $GLOBALS[ 'mdlName' ]::find( $id ), 200 );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update( Request $request, $id )
    {
        $row = $GLOBALS[ 'mdlName' ]::find( $id )->update( $GLOBALS[ 'input' ] );
        return RestResponseMessages::CRUDSuccessMessage( 'Update', $GLOBALS[ 'tblName' ], $GLOBALS[ 'mdlName' ]::all(), 200 );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy( Request $request, $id )
    {
        $row = $GLOBALS[ 'mdlName' ]::find( $id )->delete();
        return RestResponseMessages::CRUDSuccessMessage( 'Delete', $GLOBALS[ 'tblName' ], $GLOBALS[ 'mdlName' ]::all(), 200 );
    }
}
