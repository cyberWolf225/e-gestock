<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditBudgetairesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_budgetaires', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ref_depot')->index();
            $table->unsignedBigInteger('code_structure')->index();
            $table->unsignedBigInteger('ref_fam')->index();
            $table->year('exercice')->index();
            $table->string('code_gestion')->index();
            $table->string('credit_initiale');
            $table->string('consommation');
            $table->string('credit');
            $table->string('consommation_non_interfacee')->default(0)->nullable();
            $table->foreign('ref_depot')->references('ref_depot')->on('depots');
            $table->foreign('code_structure')->references('code_structure')->on('structures');
            $table->foreign('ref_fam')->references('ref_fam')->on('familles');
            $table->foreign('exercice')->references('exercice')->on('exercices');
            $table->foreign('code_gestion')->references('code_gestion')->on('gestions');
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
        Schema::dropIfExists('credit_budgetaires');
    }
}
