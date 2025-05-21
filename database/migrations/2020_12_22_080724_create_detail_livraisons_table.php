<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailLivraisonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_livraisons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('livraison_commandes_id')->index();
            $table->unsignedBigInteger('detail_cotations_id')->index();
            $table->unsignedBigInteger('sequence')->nullable();
            $table->integer('qte');
            $table->integer('qte_frs');
            $table->integer('prix_unit');
            $table->double('remise')->default(0)->nullable();
            $table->double('montant_ht')->nullable();
            $table->double('montant_ttc')->nullable();
            $table->foreign('livraison_commandes_id')->references('id')->on('livraison_commandes');
            $table->foreign('detail_cotations_id')->references('id')->on('detail_cotations');
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
        Schema::dropIfExists('detail_livraisons');
    }
}
