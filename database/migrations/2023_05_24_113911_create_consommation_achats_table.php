<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsommationAchatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consommation_achats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('detail_livraisons_id')->index();
            $table->unsignedBigInteger('livraisons_id')->index();
            $table->integer('qte');
            $table->integer('qte_distribuee')->default(0)->nullable();
            $table->foreign('detail_livraisons_id')->references('id')->on('detail_livraisons');
            $table->foreign('livraisons_id')->references('id')->on('livraisons');
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
        Schema::dropIfExists('consommation_achats');
    }
}
