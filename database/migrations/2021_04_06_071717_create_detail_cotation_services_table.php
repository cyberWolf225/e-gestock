<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailCotationServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_cotation_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cotation_services_id')->index();
            $table->unsignedBigInteger('services_id')->index();
            $table->unsignedBigInteger('code_unite')->index()->nullable();
            $table->integer('qte');
            $table->integer('prix_unit');
            $table->double('remise')->default(0)->nullable();
            $table->double('montant_ht')->nullable();
            $table->double('montant_ttc')->nullable();
            $table->foreign('cotation_services_id')->references('id')->on('cotation_services');
            $table->foreign('services_id')->references('id')->on('services');
            $table->foreign('code_unite')->references('code_unite')->on('unites');
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
        Schema::dropIfExists('detail_cotation_services');
    }
}
