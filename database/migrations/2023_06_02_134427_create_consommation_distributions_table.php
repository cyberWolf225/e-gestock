<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsommationDistributionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consommation_distributions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('consommation_achats_id')->index();
            $table->unsignedBigInteger('consommations_id')->index();
            $table->integer('qte');
            $table->foreign('consommation_achats_id')->references('id')->on('consommation_achats');
            $table->foreign('consommations_id')->references('id')->on('consommations');
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
        Schema::dropIfExists('consommation_distributions');
    }
}
