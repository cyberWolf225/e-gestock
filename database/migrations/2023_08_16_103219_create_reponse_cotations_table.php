<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReponseCotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reponse_cotations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fournisseur_demande_cotations_id')->index();
            $table->unsignedBigInteger('devises_id')->index();
            $table->decimal('montant_total_brut',20,6)->unsigned()->nullable();
            $table->decimal('taux_remise_generale',20,6)->unsigned()->nullable();
            $table->decimal('remise_generale',20,6)->unsigned()->nullable();
            $table->decimal('montant_total_net',20,6)->unsigned()->nullable();
            $table->unsignedBigInteger('tva')->nullable();
            $table->decimal('montant_total_ttc',20,6)->unsigned()->nullable();
            $table->decimal('assiete_bnc',20,6)->unsigned()->nullable();
            $table->unsignedBigInteger('taux_bnc')->nullable();
            $table->decimal('net_a_payer',20,6)->unsigned()->nullable();
            $table->boolean('acompte')->nullable();
            $table->string('taux_acompte')->nullable();
            $table->decimal('montant_acompte',20,6)->unsigned()->nullable();
            $table->string('taux_de_change')->nullable();
            $table->foreign('fournisseur_demande_cotations_id')->references('id')->on('fournisseur_demande_cotations');
            $table->foreign('devises_id')->references('id')->on('devises');
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
        Schema::dropIfExists('reponse_cotations');
    }
}
