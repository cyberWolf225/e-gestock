<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatutProfilsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statut_profils', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profils_id')->index();
            $table->unsignedBigInteger('type_statut_profils_id')->index();
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->unsignedBigInteger('profils_ids')->index();
            $table->text('commentaire')->nullable();
            $table->foreign('profils_id')->references('id')->on('profils');
            $table->foreign('type_statut_profils_id')->references('id')->on('type_statut_profils');
            $table->foreign('profils_ids')->references('id')->on('profils');
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
        Schema::dropIfExists('statut_profils');
    }
}
