<?php

namespace App\Http\Controllers\Base;

use DB;
use App;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BaseController extends Controller
{
    protected $tableNames = [ 'account', 'customer', 'product_service', 'product_category', 'account_detail_type', 'account_category_type' ];

    public function __construct() {
    	$this->middleware( function( $request, $next ) {

            $this->dbName = \Auth::user()->db_name;

            configureDBConnectionByName( $this->dbName );
            App::make( 'config' )->set( 'database.default', $this->dbName );

            $this->payee = DB::table( 'customer' )
                ->select( DB::raw( '1 as type' ), 'id', 'name' )
                ->union( DB::table( 'supplier' )->select( DB::raw( '2 as type' ), 'id', 'name' ) )
                ->get()->keyBy( 'name' );
                
            foreach ( $this->tableNames as $name ) {
                $this->$name = DB::table( $name )->get( [ 'id', 'name' ] )->keyBy( 'name' );
            }

            $GLOBALS[ 'input' ]                         =       $request->all();
            $GLOBALS[ 'input' ][ 'REQUEST_METHOD' ]     =       $request->method();

            if ( isset( $GLOBALS[ 'input' ][ 'foreign_keys' ] ) ) {
                $foreign_keys = $GLOBALS[ 'input' ][ 'foreign_keys' ];
                foreach ( $foreign_keys as $key ) {
                    $list = $this->$key;
                    $GLOBALS[ 'input' ][ $key . '_id' ] = $list[ $GLOBALS[ 'input' ][ $key ] ]->id;
                    if ( $key == 'payee' ) {
                        $GLOBALS[ 'input' ][ $key . '_type' ] = $list[ $GLOBALS[ 'input' ][ $key ] ]->type;
                    }
                }
            }

    		return $next( $request );
    	} );
    }
}
