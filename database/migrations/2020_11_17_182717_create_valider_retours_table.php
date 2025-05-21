<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateValiderRetoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('valider_retours', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profils_id')->index();
            $table->unsignedBigInteger('retours_id')->index();
            $table->integer('qte_validee');
            $table->unsignedBigInteger('prixu_retour_valide');
            $table->unsignedBigInteger('montant_retour_valide');
            $table->boolean('flag_valide')->nullable();
            $table->foreign('profils_id')->references('id')->on('profils');
            $table->foreign('retours_id')->references('id')->on('retours');
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
        Schema::dropIfExists('valider_retours');
    }
}
