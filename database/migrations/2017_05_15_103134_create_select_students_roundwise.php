<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSelectStudentsRoundwise extends Migration
{

    public function up()
    {
        Schema::create('select_students_roundwise', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('placement_id')->unsigned();
            $table->foreign('placement_id')->references('placement_id')->on('placements_primary')->onDelete('cascade');

            $table->integer('student_id')->unsigned();
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');

            $table->integer('round_no')->default(1);        //so that student is whenever inserted from application it is in round no 1..

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('select_students_roundwise');
    }

}
