<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailPerdiemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_perdiems', function (Blueprint $table) {
            $table->id();
            $table->string('nom_prenoms');
            $table->integer('montant');
            $table->unsignedBigInteger('perdiems_id')->index();
            $table->string('piece')->nullable();
            $table->string('piece_name')->nullable();
            $table->foreign('perdiems_id')->references('id')->on('perdiems');
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
        Schema::dropIfExists('detail_perdiems');
    }
}
