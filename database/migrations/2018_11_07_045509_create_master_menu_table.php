<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasterMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_menu', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('path');
            $table->string('display_name');
            $table->string('description');
            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users');
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
        Schema::dropIfExists('master_menu');
    }
}
