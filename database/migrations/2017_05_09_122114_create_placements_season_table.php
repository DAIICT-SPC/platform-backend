<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlacementsSeasonTable extends Migration
{

    public function up()
    {
        Schema::create('placements_season', function (Blueprint $table) {
            $table->increments('id');
            $table->text('title');
            $table->string('status')->default('draft');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('placements_season');
    }
}
