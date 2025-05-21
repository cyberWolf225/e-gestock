<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRetoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retours', function (Blueprint $table) {
            $table->id();
            $table->integer('qte_retour');
            $table->unsignedBigInteger('prixu_retour');
            $table->unsignedBigInteger('montant_retour');
            $table->string('observation');
            $table->unsignedBigInteger('livraisons_id')->index();
            $table->foreign('livraisons_id')->references('id')->on('livraisons');
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
        Schema::dropIfExists('retours');
    }
}
