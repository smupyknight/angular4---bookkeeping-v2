<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'attachment', function( Blueprint $table ) {
            $table->increments( 'id' )->unsigned();
            $table->tinyInteger( 'transaction_type' )->unsigned();
            $table->integer( 'transaction_id' )->unsigned();
            $table->string( 'attachment_name', 50 );
            $table->text( 'attachment_link' );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attachment');
    }
}
