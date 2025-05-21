<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAffectationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affectations', function (Blueprint $table) {
            $table->id();
            $table->string('ref_equipement')->index();
            $table->integer('index');
            $table->unsignedBigInteger('detail_immobilisations_id')->index();
            $table->unsignedBigInteger('type_affectations_id')->index();
            $table->date('date_debut')->index();
            $table->date('date_fin')->index()->nullable();
            $table->boolean('flag_actif')->nullable()->default(0);
            $table->boolean('flag_reception')->nullable()->default(0);
            $table->foreign('detail_immobilisations_id')->references('id')->on('detail_immobilisations');
            $table->foreign('ref_equipement')->references('ref_equipement')->on('equipement_immobilisers');
            $table->foreign('type_affectations_id')->references('id')->on('type_affectations');
            $table->foreign('date_debut')->references('date')->on('date_operations');
            $table->foreign('date_fin')->references('date')->on('date_operations');
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
        Schema::dropIfExists('affectations');
    }
}
