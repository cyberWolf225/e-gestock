<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBcnDetailDemandeCotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bcn_detail_demande_cotations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('detail_demande_cotations_id')->index();
            $table->unsignedBigInteger('services_id')->index();
            $table->foreign('detail_demande_cotations_id')->references('id')->on('detail_demande_cotations');
            $table->foreign('services_id')->references('id')->on('services');
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
        Schema::dropIfExists('bcn_detail_demande_cotations');
    }
}
