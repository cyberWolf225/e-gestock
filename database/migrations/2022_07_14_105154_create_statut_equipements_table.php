<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatutEquipementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statut_equipements', function (Blueprint $table) {
            $table->id();
            $table->string('ref_equipement')->index();
            $table->unsignedBigInteger('type_statut_equipements_id')->index();
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->unsignedBigInteger('profils_id')->index();
            $table->text('commentaire')->nullable();
            $table->foreign('ref_equipement')->references('ref_equipement')->on('equipement_immobilisers');
            $table->foreign('type_statut_equipements_id')->references('id')->on('type_statut_equipements');
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
        Schema::dropIfExists('statut_equipements');
    }
}
