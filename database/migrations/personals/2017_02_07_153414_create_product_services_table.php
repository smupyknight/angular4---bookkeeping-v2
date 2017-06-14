<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'product_service', function ( Blueprint $table ) {
            $table->increments( 'id' )->unsigned();
            $table->string( 'name', 50 );
            $table->string( 'sku', 50 );
            $table->double( 'price' );
            $table->integer( 'product_category_id' );
            $table->boolean( 'active' );
            $table->timestamps();

            $table->foreign( 'product_category_id' )->references( 'id' )->on( 'product_category' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( 'product_service' );
    }
}
