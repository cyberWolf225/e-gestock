<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdjudicationCommandesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adjudication_commandes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('selection_adjudications_id')->index();
            $table->unsignedBigInteger('profils_id')->index();
            $table->foreign('selection_adjudications_id')->references('id')->on('selection_adjudications');
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
        Schema::dropIfExists('adjudication_commandes');
    }
}
