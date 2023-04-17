<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProyectoFormativosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proyectoFormativo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo');

            $table->unsignedInteger('idPrograma');
            $table->foreign('idPrograma')->references('id')->on('programa');

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
        Schema::dropIfExists('proyectoFormativo');
    }
}
