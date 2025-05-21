<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTravauxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('travauxes', function (Blueprint $table) {
            $table->id();
            $table->string('num_bc')->unique()->nullable();
            $table->string('intitule')->nullable();
            $table->unsignedBigInteger('organisations_id')->index(); 
            $table->unsignedBigInteger('credit_budgetaires_id')->index();
            $table->unsignedBigInteger('ref_fam')->index();
            $table->unsignedBigInteger('devises_id')->index();
            $table->unsignedBigInteger('code_structure')->index();
            $table->unsignedBigInteger('ref_depot')->index();
            $table->string('code_gestion')->index();
            $table->year('exercice')->index();
            $table->decimal('montant_total_brut',20,6)->unsigned()->nullable();
            $table->decimal('taux_remise_generale',20,6)->unsigned()->nullable();
            $table->decimal('remise_generale',20,6)->unsigned()->nullable();
            $table->decimal('montant_total_net',20,6)->unsigned()->nullable();
            $table->decimal('tva',20,6)->unsigned()->nullable();
            $table->decimal('montant_total_ttc',20,6)->unsigned()->nullable();
            $table->decimal('net_a_payer',20,6)->unsigned()->nullable();
            $table->boolean('acompte')->nullable();
            $table->decimal('taux_acompte',20,6)->unsigned()->nullable();
            $table->decimal('montant_acompte',20,6)->unsigned()->nullable();
            $table->integer('delai');
            $table->unsignedBigInteger('periodes_id')->index();
            $table->date('date_echeance');
            $table->boolean('flag_actif')->nullable()->default(1);
            $table->date('date_livraison_prevue')->nullable();
            $table->date('date_livraison')->nullable();
            $table->date('date_retrait')->nullable();
            $table->string('taux_de_change')->nullable();
            $table->string('solde_avant_op')->nullable();
            $table->boolean('flag_engagement')->default(0)->nullable();
            $table->foreign('code_gestion')->references('code_gestion')->on('gestions');
            $table->foreign('code_structure')->references('code_structure')->on('structures');
            $table->foreign('ref_depot')->references('ref_depot')->on('depots');
            $table->foreign('ref_fam')->references('ref_fam')->on('familles');
            $table->foreign('organisations_id')->references('id')->on('organisations');
            $table->foreign('credit_budgetaires_id')->references('id')->on('credit_budgetaires');
            $table->foreign('periodes_id')->references('id')->on('periodes');
            $table->foreign('devises_id')->references('id')->on('devises');
            $table->foreign('exercice')->references('exercice')->on('exercices');
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
        Schema::dropIfExists('travauxes');
    }
}
