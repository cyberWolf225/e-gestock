<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventaireArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventaire_articles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventaires_id')->index();
            $table->unsignedBigInteger('magasin_stocks_id')->index();
            $table->unsignedBigInteger('profils_id')->index();
            $table->integer('qte_theo');
            $table->integer('qte_phys');
            $table->integer('ecart');
            $table->string('cmup_inventaire')->nullable();
            $table->string('montant_inventaire')->nullable();
            $table->text('justificatif')->nullable();
            $table->boolean('flag_valide')->nullable()->default(0);
            $table->boolean('flag_integre')->nullable()->default(0);
            $table->unsignedBigInteger('mouvements_id')->index()->nullable();
            $table->foreign('inventaires_id')->references('id')->on('inventaires');
            $table->foreign('magasin_stocks_id')->references('id')->on('magasin_stocks');
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
        Schema::dropIfExists('inventaire_articles');
    }
}
