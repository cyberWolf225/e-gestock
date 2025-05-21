<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganisationDepotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organisation_depots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ref_depot')->index();
            $table->unsignedBigInteger('organisations_id')->index();
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->boolean('flag_actif')->nullable();
            $table->foreign('ref_depot')->references('ref_depot')->on('depots');
            $table->foreign('organisations_id')->references('id')->on('organisations');
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
        Schema::dropIfExists('organisation_depots');
    }
}
