<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaitriseStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maitrise_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('magasin_stocks_id')->index();
            $table->unsignedBigInteger('type_maitrise_stocks_id')->index();
            $table->unsignedBigInteger('periodes_id')->index();
            $table->integer('valeur');
            $table->foreign('magasin_stocks_id')->references('id')->on('magasin_stocks');
            $table->foreign('type_maitrise_stocks_id')->references('id')->on('type_maitrise_stocks');
            $table->foreign('periodes_id')->references('id')->on('periodes');
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
        Schema::dropIfExists('maitrise_stocks');
    }
}
