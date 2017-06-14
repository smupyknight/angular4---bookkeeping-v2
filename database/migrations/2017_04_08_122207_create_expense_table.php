<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpenseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'expense', function ( Blueprint $table ) {
            $table->increments( 'id' )->unsigned();
            $table->date( 'date' );
            $table->tinyInteger( 'transaction_type' )->unsigned();
            $table->integer( 'payee_id' )->unsigned();
            $table->tinyInteger( 'payee_type' )->unsigned();
            $table->integer( 'account_id' )->unsigned();
            $table->double( 'total' );
            $table->text( 'statement_memo' )->nullable();
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
        Schema::dropIfExists('expense');
    }
}
