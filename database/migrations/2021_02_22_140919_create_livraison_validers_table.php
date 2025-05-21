<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLivraisonValidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('livraison_validers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profils_id')->index();
            $table->unsignedBigInteger('livraison_commandes_id')->index();
            $table->unsignedBigInteger('detail_livraisons_id')->index();
            $table->unsignedBigInteger('mouvements_id')->index()->nullable();
            $table->integer('qte');
            $table->unsignedBigInteger('prix_unit');
            $table->unsignedBigInteger('remise')->default(0)->nullable();
            $table->unsignedBigInteger('montant_ht')->nullable();
            $table->unsignedBigInteger('montant_ttc')->nullable();
            $table->foreign('profils_id')->references('id')->on('profils');
            $table->foreign('livraison_commandes_id')->references('id')->on('livraison_commandes');
            $table->foreign('detail_livraisons_id')->references('id')->on('detail_livraisons');
            $table->foreign('mouvements_id')->references('id')->on('mouvements');
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
        Schema::dropIfExists('livraison_validers');
    }
}
