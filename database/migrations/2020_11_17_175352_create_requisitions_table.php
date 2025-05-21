<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequisitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requisitions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('code_structure');
            $table->string('num_bc')->unique();
            $table->year('exercice')->index();
            $table->text('intitule')->nullable();
            $table->string('code_gestion')->nullable()->index();
            $table->unsignedBigInteger('profils_id')->index();
            $table->unsignedBigInteger('departements_id')->index()->nullable();
            $table->boolean('flag_consolide')->default(0);
            $table->string('type_beneficiaire')->nullable();
            $table->foreign('departements_id')->references('id')->on('departements');
            $table->foreign('profils_id')->references('id')->on('profils');
            $table->foreign('exercice')->references('exercice')->on('exercices');
            $table->foreign('code_structure')->references('code_structure')->on('structures');
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
        Schema::dropIfExists('requisitions');
    }
}
