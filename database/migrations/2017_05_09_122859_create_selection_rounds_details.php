<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSelectionRoundsDetails extends Migration
{

    public function up()
    {
        Schema::create('selection_rounds', function (Blueprint $table) {
            $table->increments('id')->unsigned();

            $table->integer('placement_id')->unsigned();
            $table->foreign('placement_id')->references('placement_id')->on('placements_primary')->onDelete('cascade');

            $table->integer('round_no')->unsigned();
            $table->string('round_name');
            $table->text('round_description');
            $table->boolean('round_status')->default('0')->nullable();    // 0 means incomplete and 1 means complete
            $table->date('date_of_round')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('selection_rounds');
    }
}
