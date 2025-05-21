<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFournisseurDemandeCotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fournisseur_demande_cotations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('demande_cotations_id')->index();
            $table->unsignedBigInteger('organisations_id')->index();
            $table->boolean('flag_actif')->default(1)->nullable();
            $table->foreign('demande_cotations_id')->references('id')->on('demande_cotations');
            $table->foreign('organisations_id')->references('id')->on('organisations');
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
        Schema::dropIfExists('fournisseur_demande_cotations');
    }
}
