<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDemandeFondsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demande_fonds', function (Blueprint $table) {
            $table->id();
            $table->string('num_dem')->unique();
            $table->unsignedBigInteger('code_section')->index();
            $table->unsignedBigInteger('profils_id')->index();
            $table->unsignedBigInteger('agents_id')->index();
            $table->unsignedBigInteger('credit_budgetaires_id')->index()->nullable();
            $table->unsignedBigInteger('ref_fam')->index();
            $table->unsignedBigInteger('solde_avant_op')->nullable();
            $table->year('exercice')->index();
            $table->text('intitule');
            $table->unsignedBigInteger('montant');
            $table->text('observation')->nullable();
            $table->unsignedBigInteger('moyen_paiements_id')->index()->nullable();
            $table->string('code_gestion')->index()->nullable();
            $table->boolean('terminer')->default(0);
            $table->boolean('flag_engagement')->default(0)->nullable();
            $table->foreign('code_section')->references('code_section')->on('sections');
            $table->foreign('agents_id')->references('id')->on('agents');
            $table->foreign('profils_id')->references('id')->on('profils');
            $table->foreign('credit_budgetaires_id')->references('id')->on('credit_budgetaires');
            $table->foreign('ref_fam')->references('ref_fam')->on('familles');
            $table->foreign('exercice')->references('exercice')->on('exercices');
            $table->foreign('code_gestion')->references('code_gestion')->on('gestions');
            $table->foreign('moyen_paiements_id')->references('id')->on('moyen_paiements');
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
        Schema::dropIfExists('demande_fonds');
    }
}
