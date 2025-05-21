<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMagasinStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magasin_stocks', function (Blueprint $table) {
            $table->id();
            $table->integer('qte');
            $table->unsignedBigInteger('cmup')->nullable();
            $table->unsignedBigInteger('montant')->nullable();
            $table->integer('stock_securite')->nullable();
            $table->integer('stock_alert')->nullable();
            $table->integer('stock_mini')->nullable();
            $table->unsignedBigInteger('ref_articles')->index();
            $table->unsignedBigInteger('ref_magasin')->index();
            $table->foreign('ref_articles')->references('ref_articles')->on('articles');
            $table->foreign('ref_magasin')->references('ref_magasin')->on('magasins');
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
        Schema::dropIfExists('magasin_stocks');
    }
}
