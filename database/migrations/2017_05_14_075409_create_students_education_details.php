<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsEducationDetails extends Migration
{

    public function up()
    {
        Schema::create('students_education', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('enroll_no')->unsigned();
            $table->foreign('enroll_no')->references('enroll_no')->on('students')->onDelete('cascade');

            $table->integer('education_id')->unsigned();
            $table->foreign('education_id')->references('id')->on('education')->onDelete('cascade');

            $table->text('clg_school');
            $table->double('cpi');
            $table->date('start_year');
            $table->date('end_year')->nullable();
            $table->text('drive_link');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('students_education');
    }

}
