<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBonLivraisonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bon_livraisons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('livraison_commandes_id')->index();
            $table->unsignedBigInteger('profils_id')->index();
            $table->unsignedBigInteger('sequence')->nullable();
            $table->string('name')->nullable();
            $table->string('piece');
            $table->boolean('flag_actif')->default(1);
            $table->foreign('livraison_commandes_id')->references('id')->on('livraison_commandes');
            $table->foreign('profils_id')->references('id')->on('profils');
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
        Schema::dropIfExists('bon_livraisons');
    }
}
