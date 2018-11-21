<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasterPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_permission', function (Blueprint $table) {
            $table->integer('menu')->unsigned();
            $table->foreign('menu')->references('id')->on('master_menu');
            $table->integer('authority')->unsigned();
            $table->foreign('authority')->references('id')->on('master_authority');
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
        Schema::dropIfExists('master_permission');
    }
}
