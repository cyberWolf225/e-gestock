<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSelectionAdjudicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('selection_adjudications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cotation_fournisseurs_id')->index();
            $table->unsignedBigInteger('profils_id')->index();
            $table->foreign('cotation_fournisseurs_id')->references('id')->on('cotation_fournisseurs');
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
        Schema::dropIfExists('selection_adjudications');
    }
}
