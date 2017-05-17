<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsBasicDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('students', function (Blueprint $table) {

            $table->increments('id')->unsigned();

            $table->integer('enroll_no')->unsigned()->unique()->nullable();

            $table->string('student_name')->nullable();

            $table->integer('category_id')->unsigned()->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');;       //Btech, Mtech


            $table->longText('temp_address')->nullable();
            $table->longText('perm_address')->nullable();
            $table->string('contact_no')->nullable();
            $table->date('dob')->nullable();
            $table->string('gender')->nullable();
            $table->string('category')->nullable();         //general, minor
            $table->date('enrollment_date')->nullable();

         //   $table->text('resume_link')->nullable();

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');;       //User table, as Student is also a user

            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }

}
