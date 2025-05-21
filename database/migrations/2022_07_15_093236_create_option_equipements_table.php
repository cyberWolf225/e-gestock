<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptionEquipementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('option_equipements', function (Blueprint $table) {
            $table->id();
            $table->string('ref_equipement')->index();
            $table->unsignedBigInteger('options_id')->index();
            $table->string('valeur_option');
            $table->foreign('ref_equipement')->references('ref_equipement')->on('equipement_immobilisers');
            $table->foreign('options_id')->references('id')->on('options');
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
        Schema::dropIfExists('option_equipements');
    }
}
