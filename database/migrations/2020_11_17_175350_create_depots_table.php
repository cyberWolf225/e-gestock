<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('depots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ref_depot')->unique();
            $table->string('design_dep');
            $table->string('tel_dep')->nullable();
            $table->string('adr_dep')->nullable();
            $table->string('principal')->nullable();
            $table->unsignedBigInteger('code_ville')->nullable();
            $table->foreign('code_ville')->references('code_ville')->on('villes');
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
        Schema::dropIfExists('depots');
    }
}
