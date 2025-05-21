<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatutMouvementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statut_mouvements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mouvements_id')->index();
            $table->unsignedBigInteger('type_statut_mouvements_id')->index();
            $table->unsignedBigInteger('profils_id')->index();
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->text('commentaire')->nullable();
            $table->foreign('profils_id')->references('id')->on('profils');
            $table->foreign('mouvements_id')->references('id')->on('mouvements');
            $table->foreign('type_statut_mouvements_id')->references('id')->on('type_statut_mouvements');
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
        Schema::dropIfExists('statut_mouvements');
    }
}
