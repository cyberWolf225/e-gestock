<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailAdjudicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_adjudications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cotation_fournisseurs_id')->index();
            $table->unsignedBigInteger('critere_adjudications_id')->index();
            $table->string('valeur');
            $table->foreign('cotation_fournisseurs_id')->references('id')->on('cotation_fournisseurs');
            $table->foreign('critere_adjudications_id')->references('id')->on('critere_adjudications');
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
        Schema::dropIfExists('detail_adjudications');
    }
}
