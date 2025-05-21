<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatutDemandeCotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statut_demande_cotations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('demande_cotations_id')->index();
            $table->unsignedBigInteger('type_statuts_id')->index();
            $table->unsignedBigInteger('profils_id')->index();
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->text('commentaire')->nullable();
            $table->foreign('profils_id')->references('id')->on('profils');
            $table->foreign('demande_cotations_id')->references('id')->on('demande_cotations');
            $table->foreign('type_statuts_id')->references('id')->on('type_statut_demande_cotations');
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
        Schema::dropIfExists('statut_demande_cotations');
    }
}
