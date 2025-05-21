<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailImmobilisationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_immobilisations', function (Blueprint $table) {
            $table->id();
            $table->text('intitule')->nullable();
            $table->integer('qte');
            $table->integer('qte_sortie')->nullable()->default(0);
            $table->unsignedBigInteger('prixu');
            $table->unsignedBigInteger('montant');
            $table->unsignedBigInteger('immobilisations_id')->index();
            $table->unsignedBigInteger('immobilisations_id_consolide')->index()->nullable();
            $table->unsignedBigInteger('magasin_stocks_id')->index();
            $table->unsignedBigInteger('mouvements_id')->index()->nullable();
            $table->unsignedBigInteger('beneficiaire');   
            $table->string('type_beneficiaire');   
            $table->string('echantillon')->nullable();   
            $table->text('observations')->nullable();   
            $table->foreign('immobilisations_id_consolide')->references('id')->on('immobilisations'); 
            $table->foreign('immobilisations_id')->references('id')->on('immobilisations');
            $table->foreign('magasin_stocks_id')->references('id')->on('magasin_stocks');
            $table->foreign('mouvements_id')->references('id')->on('mouvements');
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
        Schema::dropIfExists('detail_immobilisations');
    }
}
