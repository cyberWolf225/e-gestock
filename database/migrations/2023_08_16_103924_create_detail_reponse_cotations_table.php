<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailReponseCotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_reponse_cotations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reponse_cotations_id')->index();
            $table->unsignedBigInteger('detail_demande_cotations_id')->index();
            $table->integer('qte');
            $table->decimal('prix_unit',20,6)->unsigned();
            $table->decimal('remise',20,6)->unsigned()->default(0)->nullable();
            $table->decimal('montant_ht',20,6)->unsigned()->nullable();
            $table->decimal('montant_ttc',20,6)->unsigned()->nullable();
            $table->string('echantillon')->nullable();
            $table->foreign('reponse_cotations_id')->references('id')->on('reponse_cotations');
            $table->foreign('detail_demande_cotations_id')->references('id')->on('detail_demande_cotations');
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
        Schema::dropIfExists('detail_reponse_cotations');
    }
}
