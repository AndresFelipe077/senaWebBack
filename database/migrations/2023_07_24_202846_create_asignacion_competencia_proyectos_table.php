<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsignacionCompetenciaProyectosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asignacionCompetenciaProyecto', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('idCompetencia');
            $table->unsignedInteger('idProyecto');

            $table->foreign('idCompetencia')->references('id')->on('competencias')->onDelete('cascade');
            $table->foreign('idProyecto')->references('id')->on('proyectoFormativo')->onDelete('cascade');
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
        Schema::dropIfExists('asignacionCompetenciaProyecto');
    }
}
