<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilFonctionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profil_fonctions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agents_id')->index();
            $table->foreign('agents_id')->references('id')->on('agents');
            $table->unsignedBigInteger('fonctions_id')->index();
            $table->foreign('fonctions_id')->references('id')->on('fonctions');
            $table->boolean('flag_actif')->default(1);
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
        Schema::dropIfExists('profil_fonctions');
    }
}
