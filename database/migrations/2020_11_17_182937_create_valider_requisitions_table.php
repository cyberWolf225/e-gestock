<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateValiderRequisitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('valider_requisitions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profils_id')->index();
            $table->unsignedBigInteger('demandes_id')->index(); 
            $table->unsignedBigInteger('prixu');
            $table->unsignedBigInteger('montant'); 
            $table->integer('qte');
            $table->boolean('flag_valide')->nullable();
            $table->foreign('profils_id')->references('id')->on('profils');
            $table->foreign('demandes_id')->references('id')->on('demandes');
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
        Schema::dropIfExists('valider_requisitions');
    }
}
