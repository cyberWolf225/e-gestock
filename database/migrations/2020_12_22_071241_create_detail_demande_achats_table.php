<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailDemandeAchatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_demande_achats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('demande_achats_id')->index();
            $table->unsignedBigInteger('ref_articles')->index();
            $table->integer('qte_demandee');
            $table->integer('qte_accordee')->nullable();
            $table->boolean('flag_valide')->nullable();
            $table->unsignedBigInteger('profils_id')->index();
            $table->unsignedBigInteger('description_articles_id')->nullable()->index();
            $table->string('echantillon')->nullable();
            $table->foreign('demande_achats_id')->references('id')->on('demande_achats');
            $table->foreign('ref_articles')->references('ref_articles')->on('articles');
            $table->foreign('description_articles_id')->references('id')->on('description_articles');
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
        Schema::dropIfExists('detail_demande_achats');
    }
}
