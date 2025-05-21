<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommandesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->string('num_bc')->nullable();
            $table->double('delai');
            $table->unsignedBigInteger('demande_achats_id')->index();
            $table->unsignedBigInteger('periodes_id')->index();
            $table->datetime('date_echeance');
            $table->date('date_livraison_prevue')->nullable();
            $table->date('date_livraison_effective')->nullable();
            $table->unsignedBigInteger('profils_id')->index();
            $table->string('solde_avant_op')->nullable();
            $table->string('solde_apres_op')->nullable();
            $table->foreign('demande_achats_id')->references('id')->on('demande_achats');
            $table->foreign('periodes_id')->references('id')->on('periodes');
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
        Schema::dropIfExists('commandes');
    }
}
