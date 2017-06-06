<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOfferLetterDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {

            $table->increments('id');

            $table->integer('placement_id')->unsigned();
            $table->foreign('placement_id')->references('placement_id')->on('placements_primary')->onDelete('cascade');

            $table->integer('enroll_no')->unsigned();
            $table->foreign('enroll_no')->references('enroll_no')->on('students')->onDelete('cascade');

            $table->double('package')->default('0');

            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('offers');
    }
}
