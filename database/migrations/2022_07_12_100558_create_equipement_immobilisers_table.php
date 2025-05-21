<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEquipementImmobilisersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipement_immobilisers', function (Blueprint $table) {
            $table->string('ref_equipement')->primary();
            $table->unsignedBigInteger('magasin_stocks_id')->index();
            $table->year('exercice')->index();
            $table->foreign('magasin_stocks_id')->references('id')->on('magasin_stocks');
            $table->foreign('exercice')->references('exercice')->on('exercices');
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
        Schema::dropIfExists('equipement_immobilisers');
    }
}
