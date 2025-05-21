<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCotationServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cotation_services', function (Blueprint $table) {
            $table->id();
            $table->string('num_bc')->unique();
            $table->unsignedBigInteger('organisations_id')->index();
            $table->unsignedBigInteger('demande_fonds_id')->index();
            $table->double('montant_total_brut')->nullable();
            $table->double('remise_generale')->nullable();
            $table->double('montant_total_net')->nullable();
            $table->double('tva')->nullable();
            $table->double('montant_total_ttc')->nullable();
            $table->double('net_a_payer')->nullable();
            $table->boolean('acompte')->nullable();
            $table->string('taux_acompte')->nullable();
            $table->string('montant_acompte')->nullable();
            $table->double('delai');
            $table->unsignedBigInteger('periodes_id')->index();
            $table->date('date_echeance');
            $table->boolean('flag_actif')->nullable()->default(1);
            $table->date('date_livraison_prevue')->nullable();
            $table->date('date_retrait')->nullable();
            $table->foreign('organisations_id')->references('id')->on('organisations');
            $table->foreign('demande_fonds_id')->references('id')->on('demande_fonds');
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
        Schema::dropIfExists('cotation_services');
    }
}
