<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_sections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agents_id')->index();
            $table->unsignedBigInteger('sections_id')->index();
            $table->year('exercice')->index();
            $table->foreign('agents_id')->references('id')->on('agents');
            $table->foreign('sections_id')->references('id')->on('sections');
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
        Schema::dropIfExists('agent_sections');
    }
}
