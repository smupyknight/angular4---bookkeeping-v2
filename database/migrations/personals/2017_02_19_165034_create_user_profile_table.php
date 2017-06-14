<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'user_profile', function ( Blueprint $table ) {
            $table->increments( 'id' )->unsigned();
            $table->text( 'name', 255 );
            $table->text( 'email', 255 )->nullable();
            $table->boolean( 'email_verified' )->nullable();
            $table->text( 'mobile', 25 )->nullable();
            $table->boolean( 'mobile_verified' )->nullable();
            $table->text( 'password', 255 )->nullable();
            $table->text( 'address', 255 )->nullable();
            $table->text( 'city', 255 )->nullable();
            $table->text( 'country', 255 )->nullable();
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
        Schema::dropIfExists( 'user_profile' );
    }
}
