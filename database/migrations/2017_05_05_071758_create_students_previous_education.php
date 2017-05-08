<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsPreviousEducation extends Migration
{

    public function up()
    {
        Schema::create('StudentsPreviousEducation', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('enroll_no')->unsigned();
            $table->foreign('enroll_no')->references('enroll_no')->on('students')->onDelete('cascade');

            $table->longText('clg_school')->nullable();
            $table->text('education')->nullable();
            $table->string('grade_percent')->nullable();
            $table->date('start_year')->nullable();
            $table->date('end_year')->nullable();
            $table->string('drive_link')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('StudentsPreviousEducation');
    }
}
