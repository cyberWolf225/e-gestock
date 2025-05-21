<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('code_section')->unique();
            $table->unsignedBigInteger('code_structure')->index();
            $table->string('nom_section');
            $table->string('code_gestion')->index()->nullable();
            $table->foreign('code_structure')->references('code_structure')->on('structures');
            $table->foreign('code_gestion')->references('code_gestion')->on('gestions');
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
        Schema::dropIfExists('sections');
    }
}
