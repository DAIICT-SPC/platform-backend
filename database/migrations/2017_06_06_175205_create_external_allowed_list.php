<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExternalAllowedList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('external_allowed', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('placement_id')->unsigned();
            $table->foreign('placement_id')->references('placement_id')->on('placements_primary')->onDelete('cascade');

            $table->integer('enroll_no')->unsigned();
            $table->foreign('enroll_no')->references('enroll_no')->on('students')->onDelete('cascade');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

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
        Schema::dropIfExists('external_allowed');
    }
}
