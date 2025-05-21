<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBcsDetailDemandeCotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bcs_detail_demande_cotations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('detail_demande_cotations_id')->index();
            $table->unsignedBigInteger('ref_articles')->index();
            $table->unsignedBigInteger('description_articles_id')->index()->nullable();
            $table->foreign('detail_demande_cotations_id')->references('id')->on('detail_demande_cotations');
            $table->foreign('ref_articles')->references('ref_articles')->on('articles');
            $table->foreign('description_articles_id')->references('id')->on('description_articles');
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
        Schema::dropIfExists('bcs_detail_demande_cotations');
    }
}
