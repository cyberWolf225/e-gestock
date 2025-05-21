<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailMieuxDisantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_mieux_disants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mieux_disants_id')->index();
            $table->unsignedBigInteger('detail_reponse_cotations_id')->index();
            $table->foreign('mieux_disants_id')->references('id')->on('mieux_disants');
            $table->foreign('detail_reponse_cotations_id')->references('id')->on('detail_reponse_cotations');
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
        Schema::dropIfExists('detail_mieux_disants');
    }
}
