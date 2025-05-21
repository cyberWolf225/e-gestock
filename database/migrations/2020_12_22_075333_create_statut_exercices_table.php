<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatutExercicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statut_exercices', function (Blueprint $table) {
            $table->id();
            $table->year('exercice')->index();
            $table->unsignedBigInteger('type_statut_exercices_id')->index();
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->unsignedBigInteger('profils_id')->nullable();
            $table->foreign('exercice')->references('exercice')->on('exercices');
            $table->foreign('type_statut_exercices_id')->references('id')->on('type_statut_exercices');
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
        Schema::dropIfExists('statut_exercices');
    }
}
