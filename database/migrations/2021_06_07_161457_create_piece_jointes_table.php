<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePieceJointesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('piece_jointes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('type_operations_id')->index();
            $table->unsignedBigInteger('profils_id')->index();
            $table->unsignedBigInteger('subject_id')->index();
            $table->string('name')->nullable();
            $table->string('piece');
            $table->boolean('flag_actif')->default(1);
            $table->foreign('type_operations_id')->references('id')->on('type_operations');
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
        Schema::dropIfExists('piece_jointes');
    }
}
