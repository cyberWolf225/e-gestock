<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatutMagasinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statut_magasins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('magasins_id')->index();
            $table->unsignedBigInteger('type_statut_magasins_id')->index();
            $table->unsignedBigInteger('profils_id')->index();
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->text('commentaire')->nullable();
            $table->foreign('profils_id')->references('id')->on('profils');
            $table->foreign('magasins_id')->references('id')->on('magasins');
            $table->foreign('type_statut_magasins_id')->references('id')->on('type_statut_magasins');
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
        Schema::dropIfExists('statut_magasins');
    }
}
