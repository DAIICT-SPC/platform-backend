<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{

    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {

            $table->increments('id');

            $table->integer('enroll_no')->unsigned();
            $table->foreign('enroll_no')->references('enroll_no')->on('students')->onDelete('cascade');

            $table->text('project_name')->nullable();
            $table->string('duration')->nullable();
            $table->string('contribution')->nullable();
            $table->text('description')->nullable();
            $table->text('under_professor')->nullable();

            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('projects');
    }

}
