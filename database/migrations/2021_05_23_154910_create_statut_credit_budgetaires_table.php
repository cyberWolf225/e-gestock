<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatutCreditBudgetairesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statut_credit_budgetaires', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('type_operations_id')->index()->nullable();
            $table->unsignedBigInteger('operations_id')->index()->nullable();
            $table->unsignedBigInteger('credit_budgetaires_id')->index();
            $table->unsignedBigInteger('type_statut_c_budgetaires_id')->index();
            $table->string('montant')->nullable();
            $table->boolean('flag_actif')->nullable()->default(0);
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->unsignedBigInteger('profils_id')->index();
            $table->text('commentaire')->nullable();
            $table->foreign('profils_id')->references('id')->on('profils');
            $table->foreign('type_operations_id')->references('id')->on('type_operations');
            $table->foreign('type_statut_c_budgetaires_id')->references('id')->on('type_statut_credit_budgetaires');
            $table->foreign('credit_budgetaires_id')->references('id')->on('credit_budgetaires');
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
        Schema::dropIfExists('statut_credit_budgetaires');
    }
}
