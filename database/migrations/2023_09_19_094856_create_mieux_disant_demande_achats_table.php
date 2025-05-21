<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMieuxDisantDemandeAchatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mieux_disant_demande_achats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mieux_disants_id')->index();
            $table->unsignedBigInteger('demande_achats_id')->index();
            $table->foreign('mieux_disants_id')->references('id')->on('mieux_disants');
            $table->foreign('demande_achats_id')->references('id')->on('demande_achats');
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
        Schema::dropIfExists('mieux_disant_demande_achats');
    }
}
