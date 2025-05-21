<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLivraisonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('livraisons', function (Blueprint $table) {
            $table->id();
            $table->integer('qte');
            $table->integer('qte_recue')->default(0);
            $table->unsignedBigInteger('prixu');
            $table->unsignedBigInteger('montant');
            $table->boolean('statut')->nullable();
            $table->unsignedBigInteger('profils_id')->index();
            $table->unsignedBigInteger('mouvements_id')->index()->nullable();
            $table->unsignedBigInteger('demandes_id')->index();
            $table->datetime('date_reception')->nullable();
            $table->text('commentaire')->nullable();
            $table->foreign('mouvements_id')->references('id')->on('mouvements');
            $table->foreign('profils_id')->references('id')->on('profils');
            $table->foreign('demandes_id')->references('id')->on('demandes');
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
        Schema::dropIfExists('livraisons');
    }
}
