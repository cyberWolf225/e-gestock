<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDemandesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demandes', function (Blueprint $table) {
            $table->id();
            $table->text('intitule')->nullable();
            $table->integer('qte');
            $table->unsignedBigInteger('prixu');
            $table->unsignedBigInteger('montant');
            $table->unsignedBigInteger('requisitions_id')->index();
            $table->unsignedBigInteger('requisitions_id_consolide')->index()->nullable();
            $table->unsignedBigInteger('magasin_stocks_id')->index();
            $table->unsignedBigInteger('profils_id')->index();   
            $table->foreign('requisitions_id_consolide')->references('id')->on('requisitions');
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
        Schema::dropIfExists('demandes');
    }
}
