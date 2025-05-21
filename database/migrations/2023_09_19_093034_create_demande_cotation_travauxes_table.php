<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDemandeCotationTravauxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demande_cotation_travauxes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('demande_cotations_id')->index();
            $table->unsignedBigInteger('travauxes_id')->index();
            $table->foreign('demande_cotations_id')->references('id')->on('demande_cotations');
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
        Schema::dropIfExists('demande_cotation_travauxes');
    }
}
