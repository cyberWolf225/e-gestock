<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatutInventairesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statut_inventaires', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventaire_articles_id')->index();
            $table->unsignedBigInteger('type_statut_inventaires_id')->index();
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->unsignedBigInteger('profils_id')->index();
            $table->text('commentaire')->nullable();
            $table->foreign('inventaire_articles_id')->references('id')->on('inventaire_articles');
            $table->foreign('type_statut_inventaires_id')->references('id')->on('type_statut_inventaires');
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
        Schema::dropIfExists('statut_inventaires');
    }
}
