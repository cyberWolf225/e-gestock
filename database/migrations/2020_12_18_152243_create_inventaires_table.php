<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventairesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventaires', function (Blueprint $table) {
            $table->id();
            $table->date('debut_per');
            $table->date('fin_per');
            $table->boolean('flag_valide')->nullable()->default(0);
            $table->boolean('flag_integre')->nullable()->default(0);
            $table->unsignedBigInteger('ref_depot')->index();
            $table->foreign('ref_depot')->references('ref_depot')->on('depots');
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
        Schema::dropIfExists('inventaires');
    }
}
