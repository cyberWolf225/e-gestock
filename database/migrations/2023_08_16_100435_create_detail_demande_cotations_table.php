<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailDemandeCotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_demande_cotations', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('demande_cotations_id')->index();
            $table->unsignedBigInteger('code_unite')->index()->nullable();
            $table->integer('qte_demandee');
            $table->integer('qte_accordee');
            $table->boolean('flag_valide')->default(1);
            $table->string('echantillon')->nullable();
            $table->foreign('demande_cotations_id')->references('id')->on('demande_cotations');
            $table->foreign('code_unite')->references('code_unite')->on('unites');
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
        Schema::dropIfExists('detail_demande_cotations');
    }
}
