<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComiteReceptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comite_receptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agents_id')->index();
            $table->unsignedBigInteger('demande_achats_id')->index();
            $table->unsignedBigInteger('profils_id')->index();
            $table->unsignedBigInteger('retrait_profils_id')->index()->nullable();
            $table->boolean('flag_actif')->default(1);
            $table->foreign('agents_id')->references('id')->on('agents');
            $table->foreign('demande_achats_id')->references('id')->on('demande_achats');
            $table->foreign('profils_id')->references('id')->on('profils');
            $table->foreign('retrait_profils_id')->references('id')->on('profils');
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
        Schema::dropIfExists('comite_receptions');
    }
}
