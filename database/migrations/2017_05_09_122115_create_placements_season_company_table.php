<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlacementsSeasonCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('placements_season_company', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('placement_season_id')->unsigned();
            $table->foreign('placement_season_id')->references('id')->on('placements_season');

            $table->integer('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companys');

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
        Schema::dropIfExists('placements_season_company');
    }
}
