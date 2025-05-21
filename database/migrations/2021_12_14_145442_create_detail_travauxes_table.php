<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailTravauxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_travauxes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('travauxes_id')->index();
            $table->unsignedBigInteger('services_id')->index();
            $table->integer('qte');
            $table->decimal('prix_unit',20,6); 
            $table->boolean('flag_valide')->default(1);
            $table->decimal('remise',20,6)->default(0)->nullable();
            $table->decimal('montant_ht',20,6)->nullable();
            $table->decimal('montant_ttc',20,6)->nullable();
            $table->foreign('travauxes_id')->references('id')->on('travauxes');
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
        Schema::dropIfExists('detail_travauxes');
    }
}
