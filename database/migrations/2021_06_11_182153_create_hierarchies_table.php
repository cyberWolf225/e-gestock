<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHierarchiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hierarchies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agents_id')->index();
            $table->unsignedBigInteger('agents_id_n1')->index()->nullable();
            $table->unsignedBigInteger('agents_id_n2')->index()->nullable();
            $table->unsignedBigInteger('profils_id')->index();
            $table->boolean('flag_actif')->default(1);
            $table->foreign('profils_id')->references('id')->on('profils');
            $table->foreign('agents_id')->references('id')->on('agents');
            $table->foreign('agents_id_n1')->references('id')->on('agents');
            $table->foreign('agents_id_n2')->references('id')->on('agents');
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
        Schema::dropIfExists('hierarchies');
    }
}
