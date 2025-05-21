<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatutLivraisonCommandesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statut_livraison_commandes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('livraison_commandes_id')->index();
            $table->unsignedBigInteger('type_statut_demande_achats_id')->index();
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->unsignedBigInteger('profils_id')->index();
            $table->text('commentaire')->nullable();
            $table->foreign('livraison_commandes_id')->references('id')->on('livraison_commandes');
            $table->foreign('type_statut_demande_achats_id')->references('id')->on('type_statut_demande_achats');
            $table->foreign('profils_id')->references('id')->on('profils');
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
        Schema::dropIfExists('statut_livraison_commandes');
    }
}
