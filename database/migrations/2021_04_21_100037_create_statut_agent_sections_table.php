<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatutAgentSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statut_agent_sections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_sections_id')->index();
            $table->unsignedBigInteger('type_statut_agent_sections_id')->index();
            $table->datetime('date_debut');
            $table->datetime('date_fin')->nullable();
            $table->unsignedBigInteger('profils_id')->index();
            $table->text('commentaire')->nullable();
            $table->foreign('agent_sections_id')->references('id')->on('agent_sections');
            $table->foreign('type_statut_agent_sections_id')->references('id')->on('type_statut_agent_sections');
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
        Schema::dropIfExists('statut_agent_sections');
    }
}
