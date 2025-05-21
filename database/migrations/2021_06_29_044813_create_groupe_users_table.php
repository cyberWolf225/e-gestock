<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupeUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groupe_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dashboards_id')->index()->nullable();
            $table->unsignedBigInteger('sub_dashboards_id')->index()->nullable();
            $table->unsignedBigInteger('sub_sub_dashboards_id')->index()->nullable();
            $table->unsignedBigInteger('type_profils_id')->index();
            $table->unsignedBigInteger('profils_id')->index();
            $table->foreign('profils_id')->references('id')->on('profils');
            $table->foreign('type_profils_id')->references('id')->on('type_profils');
            $table->foreign('sub_sub_dashboards_id')->references('id')->on('sub_sub_dashboards');
            $table->foreign('sub_dashboards_id')->references('id')->on('sub_dashboards');
            $table->foreign('dashboards_id')->references('id')->on('dashboards');
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
        Schema::dropIfExists('groupe_users');
    }
}
