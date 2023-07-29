<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlaneacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('planeacion', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('idResultadoAprendizaje');
            $table->unsignedInteger('idActividadProyecto');
            $table->integer('horas');

            $table->foreign('idResultadoAprendizaje')->references('id')->on('resultadoAprendizaje');
            $table->foreign('idActividadProyecto')->references('id')->on('actividadProyecto');
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
        Schema::dropIfExists('planeacion');
    }
}
