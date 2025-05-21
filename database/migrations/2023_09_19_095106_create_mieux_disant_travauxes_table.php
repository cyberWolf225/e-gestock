<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMieuxDisantTravauxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mieux_disant_travauxes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mieux_disants_id')->index();
            $table->unsignedBigInteger('travauxes_id')->index();
            $table->foreign('mieux_disants_id')->references('id')->on('mieux_disants');
            $table->foreign('travauxes_id')->references('id')->on('travauxes');
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
        Schema::dropIfExists('mieux_disant_travauxes');
    }
}
