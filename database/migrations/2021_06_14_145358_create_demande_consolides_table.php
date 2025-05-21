<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDemandeConsolidesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demande_consolides', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('demandes_id')->index();
            $table->unsignedBigInteger('demandes_ids')->index();
            $table->text('intitule')->nullable();
            $table->integer('qte');
            $table->unsignedBigInteger('prixu');
            $table->unsignedBigInteger('montant');
            $table->unsignedBigInteger('requisitions_id')->index();
            $table->unsignedBigInteger('magasin_stocks_id')->index();
            $table->unsignedBigInteger('profils_id')->index();   
            $table->foreign('demandes_id')->references('id')->on('demandes');
            $table->foreign('demandes_ids')->references('id')->on('demandes');
            $table->foreign('requisitions_id')->references('id')->on('requisitions');
            $table->foreign('magasin_stocks_id')->references('id')->on('magasin_stocks');
            $table->foreign('profils_id')->references('id')->on('profils');
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
        Schema::dropIfExists('demande_consolides');
    }
}
