<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateValiderDemandeAchatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('valider_demande_achats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profils_id')->index();
            $table->unsignedBigInteger('detail_demande_achats_id')->index();
            $table->integer('qte_validee');
            $table->boolean('flag_valide')->nullable();
            $table->foreign('profils_id')->references('id')->on('profils');
            $table->foreign('detail_demande_achats_id')->references('id')->on('detail_demande_achats');
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
        Schema::dropIfExists('valider_demande_achats');
    }
}
