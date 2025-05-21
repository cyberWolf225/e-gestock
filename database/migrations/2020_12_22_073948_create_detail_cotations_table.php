<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailCotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_cotations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cotation_fournisseurs_id')->index();
            $table->unsignedBigInteger('ref_articles')->index();
            $table->integer('qte');
            $table->decimal('prix_unit',20,6)->unsigned();
            $table->decimal('remise',20,6)->unsigned()->default(0)->nullable();
            $table->decimal('montant_ht',20,6)->unsigned()->nullable();
            $table->decimal('montant_ttc',20,6)->unsigned()->nullable();
            $table->string('echantillon')->nullable();
            $table->foreign('cotation_fournisseurs_id')->references('id')->on('cotation_fournisseurs');
            $table->foreign('ref_articles')->references('ref_articles')->on('articles');
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
        Schema::dropIfExists('detail_cotations');
    }
}
