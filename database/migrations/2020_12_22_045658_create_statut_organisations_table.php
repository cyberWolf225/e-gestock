<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatutOrganisationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statut_organisations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organisations_id')->index();
            $table->unsignedBigInteger('type_statut_organisations_id')->index();
            $table->unsignedBigInteger('profils_id')->index();
            $table->unsignedBigInteger('profils_ids')->index();
            $table->datetime('date_debut');
            $table->datetime('date_fin')->nullable();
            $table->foreign('organisations_id')->references('id')->on('organisations');
            $table->foreign('type_statut_organisations_id')->references('id')->on('type_statut_organisations');
            $table->foreign('profils_id')->references('id')->on('profils');
            $table->foreign('profils_ids')->references('id')->on('profils');
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
        Schema::dropIfExists('statut_organisations');
    }
}
