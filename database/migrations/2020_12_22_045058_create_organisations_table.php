<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganisationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organisations', function (Blueprint $table) {
            $table->id();
            $table->integer('entnum')->unique()->nullable();
            $table->string('denomination');
            $table->string('sigle')->nullable();
            $table->unsignedBigInteger('type_organisations_id')->index();
            $table->string('contacts')->nullable();
            $table->string('adresse')->nullable();
            $table->string('num_contribuable')->nullable();
            $table->foreign('type_organisations_id')->references('id')->on('type_organisations');
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
        Schema::dropIfExists('organisations');
    }
}
