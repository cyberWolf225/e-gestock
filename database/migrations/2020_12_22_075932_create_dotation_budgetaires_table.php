<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDotationBudgetairesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dotation_budgetaires', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ref_depot')->index();
            $table->unsignedBigInteger('ref_fam')->index();
            $table->year('exercice')->index();
            $table->string('dotation_initiale');
            $table->string('consommation');
            $table->string('dotation');
            $table->foreign('ref_depot')->references('ref_depot')->on('depots');
            $table->foreign('ref_fam')->references('ref_fam')->on('familles');
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
        Schema::dropIfExists('dotation_budgetaires');
    }
}
