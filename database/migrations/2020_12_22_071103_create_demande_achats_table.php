<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDemandeAchatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demande_achats', function (Blueprint $table) {
            $table->id();
            $table->string('num_bc')->unique();
            $table->unsignedBigInteger('ref_fam')->index();
            $table->unsignedBigInteger('credit_budgetaires_id')->index()->nullable();
            $table->unsignedBigInteger('ref_depot')->index();
            $table->string('intitule');
            $table->string('code_gestion');
            $table->string('taux_acompte')->nullable();
            $table->year('exercice');
            $table->unsignedBigInteger('profils_id')->index();
            $table->unsignedBigInteger('type_achats_id')->index()->nullable();
            $table->boolean('flag_engagement')->default(0)->nullable();
            $table->foreign('credit_budgetaires_id')->references('id')->on('credit_budgetaires');
            $table->foreign('ref_fam')->references('ref_fam')->on('familles');
            $table->foreign('profils_id')->references('id')->on('profils');
            $table->foreign('type_achats_id')->references('id')->on('type_achats');
            $table->foreign('ref_depot')->references('ref_depot')->on('depots');
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
        Schema::dropIfExists('demande_achats');
    }
}
