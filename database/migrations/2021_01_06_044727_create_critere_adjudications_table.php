<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCritereAdjudicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('critere_adjudications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('criteres_id')->index();
            $table->unsignedBigInteger('demande_achats_id')->index();
            $table->foreign('criteres_id')->references('id')->on('criteres');
            $table->foreign('demande_achats_id')->references('id')->on('demande_achats');
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
        Schema::dropIfExists('critere_adjudications');
    }
}
