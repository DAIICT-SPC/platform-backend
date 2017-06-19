<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlacementsPrimaryDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('placements_primary', function (Blueprint $table) {
            $table->increments('placement_id')->unsigned();
            $table->string('job_title');
            $table->text('job_description');

            $table->dateTime('last_date_for_registration');

            $table->string('location');
            $table->integer('no_of_students')->nullable();
            $table->float('package');

            $table->integer('placement_season_id')->unsigned();
            $table->foreign('placement_season_id')->references('id')->on('placements_season');

            $table->integer('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companys');


            $table->integer('job_type_id')->unsigned();
            $table->foreign('job_type_id')->references('id')->on('job_types');

            $table->date('start_date');

            $table->date('end_date');

            $table->string('status')->default('draft');

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
        Schema::dropIfExists('placements_primary');
    }
}
