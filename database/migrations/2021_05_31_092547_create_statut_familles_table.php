<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatutFamillesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statut_familles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('familles_id')->index();
            $table->unsignedBigInteger('type_statut_familles_id')->index();
            $table->unsignedBigInteger('profils_id')->index();
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->text('commentaire')->nullable();
            $table->foreign('profils_id')->references('id')->on('profils');
            $table->foreign('familles_id')->references('id')->on('familles');
            $table->foreign('type_statut_familles_id')->references('id')->on('type_statut_familles');
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
        Schema::dropIfExists('statut_familles');
    }
}
