<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatutTravauxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statut_travauxes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('travauxes_id')->index();
            $table->unsignedBigInteger('type_statut_travauxes_id')->index();
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->unsignedBigInteger('profils_id')->index();
            $table->text('commentaire')->nullable();
            $table->foreign('travauxes_id')->references('id')->on('travauxes');
            $table->foreign('type_statut_travauxes_id')->references('id')->on('type_statut_travauxes');
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
        Schema::dropIfExists('statut_travauxes');
    }
}
