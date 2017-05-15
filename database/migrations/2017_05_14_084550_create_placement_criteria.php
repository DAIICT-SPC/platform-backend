<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlacementCriteria extends Migration
{

    public function up()
    {
        Schema::create('placement_criterias', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('placement_id')->unsigned();
            $table->foreign('placement_id')->references('placement_id')->on('placements_primary')->onDelete('cascade');

            $table->integer('education_id')->unsigned();
            $table->foreign('education_id')->references('id')->on('education')->onDelete('cascade');

            $table->double('cpi_required')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('placement_criterias');
    }

}
