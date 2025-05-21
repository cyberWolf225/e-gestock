<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsommationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consommations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('distributions_id')->index();
            $table->unsignedBigInteger('demandes_id')->index();
            $table->integer('qte');
            $table->unsignedBigInteger('prixu');
            $table->unsignedBigInteger('montant');
            $table->unsignedBigInteger('profils_id')->index();   
            $table->text('commentaire')->nullable();
            $table->foreign('demandes_id')->references('id')->on('demandes');
            $table->foreign('profils_id')->references('id')->on('profils');
            $table->foreign('distributions_id')->references('id')->on('distributions');
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
        Schema::dropIfExists('consommations');
    }
}
