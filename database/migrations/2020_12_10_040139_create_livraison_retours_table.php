<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLivraisonRetoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('livraison_retours', function (Blueprint $table) {
            $table->id();
            $table->integer('qte');
            $table->unsignedBigInteger('prixu');
            $table->unsignedBigInteger('montant');
            $table->boolean('statut')->nullable();
            $table->string('observation')->nullable();
            $table->unsignedBigInteger('profils_id')->index();
            $table->unsignedBigInteger('retours_id')->index();
            $table->foreign('profils_id')->references('id')->on('profils');
            $table->foreign('retours_id')->references('id')->on('retours');
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
        Schema::dropIfExists('livraison_retours');
    }
}
