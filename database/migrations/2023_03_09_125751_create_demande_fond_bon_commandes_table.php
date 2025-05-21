<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDemandeFondBonCommandesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demande_fond_bon_commandes', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('demande_fonds_id')->index();
            $table->unsignedBigInteger('type_operations_id')->index();
            $table->unsignedBigInteger('operations_id');
            $table->boolean('flag_actif')->default(1);
            $table->foreign('type_operations_id')->references('id')->on('type_operations');
            $table->foreign('demande_fonds_id')->references('id')->on('demande_fonds');
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
        Schema::dropIfExists('demande_fond_bon_commandes');
    }
}
