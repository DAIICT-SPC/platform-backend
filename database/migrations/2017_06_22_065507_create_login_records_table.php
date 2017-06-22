<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoginRecordsTable extends Migration
{

    public function up()
    {
        Schema::create('login_records', function (Blueprint $table) {

            $table->increments('id');

            $table->integer('from_id')->unsigned();
            $table->foreign('from_id')->references('id')->on('users')->onDelete('cascade');;       //User table, as Student is also a user

            $table->integer('to_id')->unsigned();
            $table->foreign('to_id')->references('id')->on('users')->onDelete('cascade');;       //User table, as Student is also a user

            $table->longText('reason');

            $table->timestamps();

        });
    }

    public function down()
    {

        Schema::dropIfExists('login_records');

    }
}
