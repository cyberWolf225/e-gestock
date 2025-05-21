<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreselectionSoumissionnairesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preselection_soumissionnaires', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('critere_adjudications_id')->index();
            $table->unsignedBigInteger('organisations_id')->index()->nullable();
            $table->foreign('critere_adjudications_id')->references('id')->on('critere_adjudications');
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
        Schema::dropIfExists('preselection_soumissionnaires');
    }
}
