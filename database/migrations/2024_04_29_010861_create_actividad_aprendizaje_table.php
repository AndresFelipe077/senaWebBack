<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActividadAprendizajeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actividadAprendizaje', function (Blueprint $table) {
            $table->increments('id');
            $table->string ('NombreAA',20);
            $table->string('codigoAA',50);

            $table->unsignedInteger('idPlaneacion');
            $table->foreign('idPlaneacion')->references('id')->on('planeacion');

            $table->unsignedInteger('idEstado');
            $table->foreign('idEstado')->references('id')->on('estado');

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
        Schema::dropIfExists('actividadAprendizaje');
    }
}
