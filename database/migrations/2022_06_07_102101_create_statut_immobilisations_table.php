<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatutImmobilisationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statut_immobilisations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('immobilisations_id')->index();
            $table->unsignedBigInteger('type_statut_requisitions_id')->index();
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->unsignedBigInteger('profils_id')->index();
            $table->text('commentaire')->nullable();
            $table->foreign('immobilisations_id')->references('id')->on('immobilisations');
            $table->foreign('type_statut_requisitions_id')->references('id')->on('type_statut_requisitions');
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
        Schema::dropIfExists('statut_immobilisations');
    }
}
