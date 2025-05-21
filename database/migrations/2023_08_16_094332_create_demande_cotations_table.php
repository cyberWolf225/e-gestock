<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDemandeCotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demande_cotations', function (Blueprint $table) {
            $table->id();
            $table->string('num_dem')->unique()->nullable();
            $table->text('intitule')->nullable();
            $table->unsignedBigInteger('type_operations_id')->index(); 
            $table->unsignedBigInteger('credit_budgetaires_id')->index();
            $table->unsignedBigInteger('ref_depot')->index();
            $table->unsignedBigInteger('periodes_id')->index()->nullable();
            $table->integer('delai')->nullable();
            $table->date('date_echeance')->nullable();
            $table->boolean('flag_actif')->nullable()->default(1);
            $table->integer('taux_acompte')->nullable()->default(0);
            $table->foreign('ref_depot')->references('ref_depot')->on('depots');
            $table->foreign('type_operations_id')->references('id')->on('type_operations');
            $table->foreign('credit_budgetaires_id')->references('id')->on('credit_budgetaires');
            $table->foreign('periodes_id')->references('id')->on('periodes');
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
        Schema::dropIfExists('demande_cotations');
    }
}
