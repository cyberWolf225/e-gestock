<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMieuxDisantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mieux_disants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reponse_cotations_id')->index();
            $table->decimal('montant_total_brut',20,6)->unsigned()->nullable();
            $table->decimal('remise_generale',20,6)->unsigned()->nullable();
            $table->decimal('montant_total_net',20,6)->unsigned()->nullable();
            $table->decimal('montant_total_ttc',20,6)->unsigned()->nullable();
            $table->decimal('assiete_bnc',20,6)->unsigned()->nullable();
            $table->decimal('net_a_payer',20,6)->unsigned()->nullable();
            $table->decimal('montant_acompte',20,6)->unsigned()->nullable();
            $table->foreign('reponse_cotations_id')->references('id')->on('reponse_cotations');
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
        Schema::dropIfExists('mieux_disants');
    }
}
