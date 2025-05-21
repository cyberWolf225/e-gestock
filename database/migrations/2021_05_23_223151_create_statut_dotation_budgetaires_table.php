<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatutDotationBudgetairesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statut_dotation_budgetaires', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('type_operations_id')->index()->nullable();
            $table->unsignedBigInteger('operations_id')->index()->nullable();
            $table->unsignedBigInteger('dotation_budgetaires_id')->index();
            $table->unsignedBigInteger('type_statut_d_budgetaires_id')->index();
            $table->string('montant')->nullable();
            $table->boolean('flag_actif')->nullable()->default(0);
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->unsignedBigInteger('profils_id')->index();
            $table->text('commentaire')->nullable();
            $table->foreign('profils_id')->references('id')->on('profils');
            $table->foreign('type_operations_id')->references('id')->on('type_operations');
            $table->foreign('type_statut_d_budgetaires_id')->references('id')->on('type_statut_dotation_budgetaires');
            $table->foreign('dotation_budgetaires_id')->references('id')->on('dotation_budgetaires');
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
        Schema::dropIfExists('statut_dotation_budgetaires');
    }
}
