<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerdiemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('perdiems', function (Blueprint $table) {
            $table->id();
            $table->string('num_pdm')->unique();
            $table->text('libelle');
            $table->integer('num_or');
            $table->string('code_gestion');
            $table->year('exercice')->index();
            $table->unsignedBigInteger('ref_fam')->index();
            $table->unsignedBigInteger('code_structure')->index();
            $table->unsignedBigInteger('solde_avant_op')->nullable();
            $table->unsignedBigInteger('credit_budgetaires_id')->index()->nullable();
            $table->integer('montant_total')->nullable();
            $table->boolean('flag_engagement')->default(0)->nullable();
            $table->foreign('credit_budgetaires_id')->references('id')->on('credit_budgetaires');
            $table->foreign('ref_fam')->references('ref_fam')->on('familles');
            $table->foreign('exercice')->references('exercice')->on('exercices');
            $table->foreign('code_structure')->references('code_structure')->on('structures');
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
        Schema::dropIfExists('perdiems');
    }
}
